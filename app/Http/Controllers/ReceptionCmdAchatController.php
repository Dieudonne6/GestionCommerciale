<?php

namespace App\Http\Controllers;

use App\Models\ReceptionCmdAchat;
use App\Models\DetailReceptionCmdAchat;
use App\Models\CommandeAchat;
use App\Models\DetailCommandeAchat;
use App\Models\Magasin;
use App\Models\Exercice;
use App\Models\Stocke;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Params;

use Carbon\Carbon;

class ReceptionCmdAchatController extends Controller
{
    public function index()
    {
        try {
            // Exercice actif
            $exerciceActif = Exercice::where('statutExercice', 1)->first();

            if (!$exerciceActif) {
                return redirect()->back()->with('erreur', 'Aucun exercice actif.');
            }

            // Réceptions de l'exercice actif
            $receptions = ReceptionCmdAchat::with([
                    'commandeAchat.fournisseur',
                    'detailReceptionCmdAchat.detailCommandeAchat.produit',
                    'exercice',
                    'utilisateur'
                ])
                ->where('idExercice', $exerciceActif->idExercice)
                ->orderBy('date', 'desc')
                ->get();

            // Commandes validées ou en cours de l'exercice actif
            $commandes = CommandeAchat::with(['fournisseur', 'detailCommandeAchat.produit'])
                ->whereIn('statutCom', ['validée', 'en cours'])
                ->where('idExercice', $exerciceActif->idExercice)
                ->get();

            // Magasins (si tu veux filtrer par exercice, ajoute ->where('idExercice', $exerciceActif->idExercice))
            $magasins = Magasin::all();

            // Exercices actifs
            $exercices = Exercice::where('statutExercice', 'actif')->get();

            return view('pages.Fournisseur&Achat.gestion_receptions', compact(
                'receptions',
                'commandes',
                'magasins',
                'exercices'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('erreur', 'Erreur lors du chargement des données: ' . $e->getMessage());
        }
    }  

    public function getCommandeDetails($idCommande)
    {
        try {
            $commande = CommandeAchat::with(['detailCommandeAchat.produit'])
                ->findOrFail($idCommande);
                // if (!in_array($commande->statutCom, ['validée', 'en cours'])) {
                //     return response()->json([
                //         'success' => false,
                //         'message' => 'Commande non valide'
                //     ], 400);
                // }

            $details = $commande->detailCommandeAchat
                ->where('qteRestante', '>', 0)
                ->map(function ($detail) {
                    // Récupérer le magasin depuis stockes
                    $magasin = DB::table('stockes')
                        ->where('idPro', $detail->idPro)
                        ->value('idMag');

                    return [
                        'idDetailCom' => $detail->idDetailCom,
                        'produit' => $detail->produit->libelle,
                        'qteCmd' => $detail->qteCmd,
                        'qteRestante' => $detail->qteRestante,
                        'prixUnit' => $detail->prixUnit,
                        'idMag' => $magasin, 
                        'expiration' => null,
                        'alert'=> null,
                    ];
                })
                ->values();

            if ($details->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune quantité restante pour ce produit'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'details' => $details
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour les statuts de la réception et de la commande
     */
   private function updateStatuts(ReceptionCmdAchat $reception)
    {
        $commande = $reception->commandeAchat;
        $totalQteRestante = $commande->detailCommandeAchat->sum('qteRestante');

        if ($totalQteRestante <= 0) {

            //  TOUTES les réceptions passent à complète
            ReceptionCmdAchat::where('idCommande', $commande->idCommande)
                ->update(['statutRecep' => 'complète']);

            // commande complète
            $commande->update(['statutCom' => 'complète']);

        } else {

            // réception courante reste en cours
            $reception->update(['statutRecep' => 'en cours']);

            // commande partiellement reçue
            $commande->update(['statutCom' => 'en cours']);
        }
    }


    public function store(Request $request)
    {
        // dd($request->all());

        
        try {                       
            $request->validate([
                'date' => 'required|date',
                'reference' => 'required|string|max:255',
                'numBordereauLivraison' => 'required|string|max:255|unique:reception_cmd_achats,numBordereauLivraison',
                // 'idExercice' => 'required|exists:exercices,idExercice',
                'idCommande' => 'required|exists:commande_achats,idCommande',
                'details' => 'required|array|min:1',
                // 'details.*.idDetailCom' => 'required|exists:detail_commande_achats,idDetailCom',
                'details.*.qteReceptionne' => 'required|numeric|min:0',
                'details.*.prixUnit' => 'required|numeric|min:0',
                'details.*.iddetailcom' => 'required|numeric|min:0',
                'details.*.expiration' => 'nullable|date|after:today',

                // 'details.*.idMag' => 'required|exists:magasins,idMag',
            ]);

            DB::transaction(function () use ($request) {
                // Vérification de la commande
                $commande = CommandeAchat::with('detailCommandeAchat')->findOrFail($request['idCommande']);

                $idExercice = Exercice::where('statutExercice', 1)
                ->firstOrFail()
                ->idExercice;  

                // if ($commande->statutCom !== 'validée' && $commande->statutCom !== 'en cours') {
                //     throw new \Exception('La commande doit être validée ou en cours pour créer une réception');
                // }

                // Création de la réception
                $reception = ReceptionCmdAchat::create([
                    'date' => $request['date'],
                    'reference' => $request['reference'],
                    'numBordereauLivraison' => $request['numBordereauLivraison'],
                    'statutRecep' => 'en cours',
                    'idExercice' => $idExercice,
                    'idCommande' => $request['idCommande'],
                    'idU' => auth()->id(),
                ]);

                // Traitement des lignes
                $param = Params::first();

                if (!$param || !$param->delai_alerte) {
                    throw new \Exception("Le délai d’alerte n’est encore pas défini dans les paramètres.");
                }

                $delaiAlerte = $param->delai_alerte;

                foreach ($request['details'] as $d) {

                    $dc = DetailCommandeAchat::with('produit')->findOrFail($d['iddetailcom']);

                    // vérification
                    if ($d['qteReceptionne'] > $dc->qteRestante) {
                        throw new \Exception(
                            'La quantité reçue ne peut pas être supérieure à la quantité restante pour ' . $dc->produit->libelle
                        );
                    }

                    $expiration = $d['expiration'] ?? null;
                    $alert = null;

                    if ($expiration) {
                        $alert = Carbon::parse($expiration)
                            ->subDays($delaiAlerte)
                            ->format('Y-m-d');
                    }

                    // détail réception
                    DetailReceptionCmdAchat::create([
                        'idRecep' => $reception->idRecep,
                        'idDetailCom' => $d['iddetailcom'],
                        'qteReceptionne' => $d['qteReceptionne'],
                        'prixUnit' => $d['prixUnit'],
                        'expiration' => $expiration,
                        'alert' => $alert,
                    ]);

                    // maj qteRestante
                    $dc->qteRestante -= $d['qteReceptionne'];
                    $dc->save();

                    // maj stock
                    $stock = Stocke::where('idPro', $dc->idPro)->lockForUpdate()->first();

                    if ($stock) {
                        $stock->qteStocke += $d['qteReceptionne'];
                        $stock->save();
                    } else {
                        Stocke::create([
                            'idPro' => $dc->idPro,
                            'qteStocke' => $d['qteReceptionne'],
                        ]);
                    }
                }

                // Mise à jour des statuts
                $this->updateStatuts($reception);
            });

            return redirect()->route('receptions.index')
                ->with('status', 'Réception créée avec succès.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('erreur', 'Erreur lors de la création de la réception: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $reception = ReceptionCmdAchat::with(['detailReceptionCmdAchat', 'commandeAchat'])->findOrFail($id);

            if ($reception->statutRecep === 'complète') {
                return redirect()->route('receptions.index')
                    ->with('erreur', 'Impossible de modifier une réception complète.');
            }

            $validator = Validator::make($request->all(), [
                'date' => 'required|date|before_or_equal:today',
                'reference' => 'required|string|max:255|unique:reception_cmd_achats,reference,' . $id . ',idRecep',
                'numBordereauLivraison' => 'required|string|max:255|unique:reception_cmd_achats,numBordereauLivraison,' . $id . ',idRecep',
                'details' => 'required|array|min:1',
                'details.*.idDetailCom' => 'required|exists:detail_commande_achats,idDetailCom',
                'details.*.qteReceptionne' => 'required|numeric|min:1',
                'details.*.prixUnit' => 'required|numeric|min:0',
                // 'details.*.idMag' => 'required|exists:magasins,idMag',
            ], [
                'date.before_or_equal' => 'La date de réception ne peut pas être future',
                'reference.unique' => 'Cette référence de BL existe déjà',
                'numBordereauLivraison.unique' => 'Ce numéro de bordereau existe déjà',
                'details.required' => 'Au moins une ligne de réception est requise',
                'details.*.qteReceptionne.min' => 'La quantité doit être supérieure à 0',
                'details.*.prixUnit.min' => 'Le prix unitaire doit être positif',
                // 'details.*.idMag.required' => 'Le magasin est obligatoire',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();

            DB::transaction(function () use ($reception, $data) {

                // 1️ ANNULATION des anciens effets
                foreach ($reception->detailReceptionCmdAchat as $old) {

                    $dc = $old->detailCommandeAchat;

                    // restaurer qteRestante
                    $dc->qteRestante += $old->qteReceptionne;
                    $dc->save();

                    // restaurer stock
                    $stock = Stocke::where('idPro', $dc->idPro)->lockForUpdate()->first();
                    if ($stock) {
                        $stock->qteStocke -= $old->qteReceptionne;
                        $stock->save();
                    }
                }

                // MAJ réception
                $reception->update([
                    'date' => $data['date'],
                    'reference' => $data['reference'],
                    'numBordereauLivraison' => $data['numBordereauLivraison'],
                ]);

                // supprimer anciens détails
                $reception->detailReceptionCmdAchat()->delete();

                // 2️ APPLICATION des nouvelles valeurs
                foreach ($data['details'] as $d) {

                    $dc = DetailCommandeAchat::with('produit')->findOrFail($d['idDetailCom']);

                    if ($d['qteReceptionne'] > $dc->qteRestante) {
                        throw new \Exception(
                            'Quantité invalide pour ' . $dc->produit->libelle
                        );
                    }

                    DetailReceptionCmdAchat::create([
                        'idRecep' => $reception->idRecep,
                        'idDetailCom' => $d['idDetailCom'],
                        'qteReceptionne' => $d['qteReceptionne'],
                        'prixUnit' => $d['prixUnit'],
                    ]);

                    // diminuer qteRestante
                    $dc->qteRestante -= $d['qteReceptionne'];
                    $dc->save();

                    // augmenter stock
                    $stock = Stocke::where('idPro', $dc->idPro)->lockForUpdate()->first();
                    if ($stock) {
                        $stock->qteStocke += $d['qteReceptionne'];
                        $stock->save();
                    }
                }

                $this->updateStatuts($reception);
            });


            return redirect()->route('receptions.index')
                ->with('status', 'Réception mise à jour avec succès.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('erreur', 'Erreur lors de la mise à jour de la réception: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $reception = ReceptionCmdAchat::with('detailReceptionCmdAchat.detailCommandeAchat')
                ->findOrFail($id);

            if ($reception->statutRecep === 'complète') {
                return back()->with('erreur', 'Impossible de supprimer une réception complète.');
            }

            DB::transaction(function () use ($reception) {

                foreach ($reception->detailReceptionCmdAchat as $det) {

                    $dc = $det->detailCommandeAchat;

                    // restauration qteRestante
                    $dc->qteRestante += $det->qteReceptionne;
                    $dc->save();

                    // mise à jour stock
                    $stock = Stocke::where('idPro', $dc->idPro)->lockForUpdate()->first();
                    if ($stock) {
                        $stock->qteStocke -= $det->qteReceptionne;
                        $stock->save();
                    }
                }

                // suppression détails puis réception
                $reception->detailReceptionCmdAchat()->delete();
                $reception->delete();
            });

            return redirect()->route('receptions.index')
                ->with('status', 'Réception supprimée avec succès.');

        } catch (\Exception $e) {
            return back()->with('erreur', $e->getMessage());
        }
    }

}