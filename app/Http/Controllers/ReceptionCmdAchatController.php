<?php

namespace App\Http\Controllers;

use App\Models\ReceptionCmdAchat;
use App\Models\DetailReceptionCmdAchat;
use App\Models\CommandeAchat;
use App\Models\DetailCommandeAchat;
use App\Models\Magasin;
use App\Models\Exercice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReceptionCmdAchatController extends Controller
{
    public function index()
    {
        try {
            $receptions = ReceptionCmdAchat::with([
                'commandeAchat.fournisseur',
                'detailReceptionCmdAchat.detailCommandeAchat.produit',
                'exercice',
                'utilisateur'
            ])
                ->orderBy('date', 'desc')
                ->get();

            // Récupérer les commandes validées uniquement
            $commandes = CommandeAchat::with(['fournisseur', 'detailCommandeAchat.produit'])
                ->whereIn('statutCom', ['validée', 'en cours'])
                ->get();

            $magasins = Magasin::all();
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
            $commande = CommandeAchat::with(['detailCommandeAchat.produit' => function ($q) {
                $q->where('qteRestante', '>', 0);
            }])->findOrFail($idCommande);

            if ($commande->statutCom !== 'validée' && $commande->statutCom !== 'en cours') {
                return response()->json([
                    'success' => false,
                    'message' => 'La commande doit être validée ou en cours pour créer une réception'
                ], 400);
            }

            $details = $commande->detailCommandeAchat->map(function ($detail) {
                return [
                    'idDetailCom' => $detail->idDetailCom,
                    'produit' => $detail->produit->libelle,
                    'qteCmd' => $detail->qteCmd,
                    'qteRestante' => $detail->qteRestante,
                    'prixUnit' => $detail->prixUnit,
                    'qteRecue' => $detail->qteRecue
                ];
            })->filter(function ($detail) {
                return $detail['qteRestante'] > 0;
            })->values();

            if ($details->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune quantité restante à réceptionner pour cette commande'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'details' => $details
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails: ' . $e->getMessage()
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
        $totalQteRecue = $commande->detailCommandeAchat->sum('qteRecue');

        // Mise à jour du statut de la réception
        if ($totalQteRestante <= 0) {
            $reception->update(['statutRecep' => 'complète']);
        } else {
            $reception->update(['statutRecep' => 'en cours']);
        }

        // Mise à jour du statut de la commande
        if ($totalQteRestante <= 0) {
            $commande->update(['statutCom' => 'complète']);
        } else if ($totalQteRecue > 0) {
            $commande->update(['statutCom' => 'en cours']);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'date' => 'required|date|before_or_equal:today',
                'reference' => 'required|string|max:255|unique:reception_cmd_achats,reference',
                'numBordereauLivraison' => 'required|string|max:255|unique:reception_cmd_achats,numBordereauLivraison',
                'idExercice' => 'required|exists:exercices,idExercice',
                'idCommande' => 'required|exists:commande_achats,idCommande',
                'details' => 'required|array|min:1',
                'details.*.idDetailCom' => 'required|exists:detail_commande_achats,idDetailCom',
                'details.*.qteReceptionne' => 'required|numeric|min:1',
                'details.*.prixUnit' => 'required|numeric|min:0',
                'details.*.idMagasin' => 'required|exists:magasins,idMagasin',
            ], [
                'date.before_or_equal' => 'La date de réception ne peut pas être future',
                'reference.unique' => 'Cette référence de BL existe déjà',
                'numBordereauLivraison.unique' => 'Ce numéro de bordereau existe déjà',
                'details.required' => 'Au moins une ligne de réception est requise',
                'details.*.qteReceptionne.min' => 'La quantité doit être supérieure à 0',
                'details.*.prixUnit.min' => 'Le prix unitaire doit être positif',
                'details.*.idMagasin.required' => 'Le magasin est obligatoire',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();

            DB::transaction(function () use ($data) {
                // Vérification de la commande
                $commande = CommandeAchat::with('detailCommandeAchat')->findOrFail($data['idCommande']);

                if ($commande->statutCom !== 'validée' && $commande->statutCom !== 'en cours') {
                    throw new \Exception('La commande doit être validée ou en cours pour créer une réception');
                }

                // Création de la réception
                $reception = ReceptionCmdAchat::create([
                    'date' => $data['date'],
                    'reference' => $data['reference'],
                    'numBordereauLivraison' => $data['numBordereauLivraison'],
                    'statutRecep' => 'en cours',
                    'idExercice' => $data['idExercice'],
                    'idCommande' => $data['idCommande'],
                    'idU' => auth()->id(),
                ]);

                // Traitement des lignes
                foreach ($data['details'] as $d) {
                    $dc = DetailCommandeAchat::with('produit')->findOrFail($d['idDetailCom']);

                    // Vérification de la quantité
                    if ($d['qteReceptionne'] > $dc->qteRestante) {
                        throw new \Exception('La quantité reçue ne peut pas être supérieure à la quantité restante pour le produit ' . $dc->produit->libelle);
                    }

                    // Création du détail de réception
                    DetailReceptionCmdAchat::create([
                        'idRecep' => $reception->idRecep,
                        'idDetailCom' => $d['idDetailCom'],
                        'qteReceptionne' => $d['qteReceptionne'],
                        'prixUnit' => $d['prixUnit'],
                        'idMagasin' => $d['idMagasin'],
                    ]);

                    // Mise à jour des quantités
                    $dc->decrement('qteRestante', $d['qteReceptionne']);
                    $dc->increment('qteRecue', $d['qteReceptionne']);
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
                'details.*.idMagasin' => 'required|exists:magasins,idMagasin',
            ], [
                'date.before_or_equal' => 'La date de réception ne peut pas être future',
                'reference.unique' => 'Cette référence de BL existe déjà',
                'numBordereauLivraison.unique' => 'Ce numéro de bordereau existe déjà',
                'details.required' => 'Au moins une ligne de réception est requise',
                'details.*.qteReceptionne.min' => 'La quantité doit être supérieure à 0',
                'details.*.prixUnit.min' => 'Le prix unitaire doit être positif',
                'details.*.idMagasin.required' => 'Le magasin est obligatoire',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();

            DB::transaction(function () use ($reception, $data) {
                // Restauration des quantités avant modification
                foreach ($reception->detailReceptionCmdAchat as $old) {
                    $dc = $old->detailCommandeAchat;
                    $dc->increment('qteRestante', $old->qteReceptionne);
                    $dc->decrement('qteRecue', $old->qteReceptionne);
                }

                // MAJ réception
                $reception->update([
                    'date' => $data['date'],
                    'reference' => $data['reference'],
                    'numBordereauLivraison' => $data['numBordereauLivraison'],
                ]);

                // Suppression anciens détails
                $reception->detailReceptionCmdAchat()->delete();

                // Création nouveaux détails
                foreach ($data['details'] as $d) {
                    $dc = DetailCommandeAchat::with('produit')->findOrFail($d['idDetailCom']);

                    if ($d['qteReceptionne'] > $dc->qteRestante) {
                        throw new \Exception('La quantité reçue ne peut pas être supérieure à la quantité restante pour le produit ' . $dc->produit->libelle);
                    }

                    DetailReceptionCmdAchat::create([
                        'idRecep' => $reception->idRecep,
                        'idDetailCom' => $d['idDetailCom'],
                        'qteReceptionne' => $d['qteReceptionne'],
                        'prixUnit' => $d['prixUnit'],
                        'idMagasin' => $d['idMagasin'],
                    ]);

                    $dc->decrement('qteRestante', $d['qteReceptionne']);
                    $dc->increment('qteRecue', $d['qteReceptionne']);
                }

                // Mise à jour des statuts
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
            $reception = ReceptionCmdAchat::with(['detailReceptionCmdAchat', 'commandeAchat'])->findOrFail($id);

            if ($reception->statutRecep === 'complète') {
                return redirect()->route('receptions.index')
                    ->with('erreur', 'Impossible de supprimer une réception complète.');
            }

            DB::transaction(function () use ($reception) {
                // Restauration quantités
                foreach ($reception->detailReceptionCmdAchat as $det) {
                    $dc = $det->detailCommandeAchat;
                    $dc->increment('qteRestante', $det->qteReceptionne);
                    $dc->decrement('qteRecue', $det->qteReceptionne);
                }

                // Suppression réception & détails
                $reception->detailReceptionCmdAchat()->delete();
                $reception->delete();

                // Mise à jour des statuts de la commande
                $commande = $reception->commandeAchat;
                $totalQteRestante = $commande->detailCommandeAchat->sum('qteRestante');
                $totalQteRecue = $commande->detailCommandeAchat->sum('qteRecue');

                if ($totalQteRestante > 0) {
                    $commande->update(['statutCom' => 'en cours']);
                } else if ($totalQteRecue > 0) {
                    $commande->update(['statutCom' => 'complète']);
                } else {
                    $commande->update(['statutCom' => 'validée']);
                }
            });

            return redirect()->route('receptions.index')
                ->with('status', 'Réception supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('erreur', 'Erreur lors de la suppression de la réception: ' . $e->getMessage());
        }
    }
}