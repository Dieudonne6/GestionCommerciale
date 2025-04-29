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

class ReceptionCmdAchatController extends Controller
{
    public function index()
    {
        $receptions = ReceptionCmdAchat::with(['commandeAchat', 'utilisateur', 'exercice'])
            ->orderBy('date', 'desc')
            ->get();

        return view('pages.Fournisseur&Achat.gestion_receptions', compact('receptions'));
    }

    public function create()
    {
        $commandes = CommandeAchat::where('statutCom', 'validée')
            ->whereDoesntHave('receptionCmdAchat', function ($query) {
                $query->where('statutRecep', 'complète');
            })
            ->with(['lignes.produit', 'lignes' => function ($query) {
                $query->where('qteRestante', '>', 0);
            }])
            ->get();

        $magasins = Magasin::all();
        $exercices = Exercice::where('statutExercice', 'actif')->get();

        return view('pages.Fournisseur&Achat.reception', compact('commandes', 'magasins', 'exercices'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'dateC' => 'required|date|before_or_equal:today',
                'referenceC' => 'required|string|max:255|unique:reception_cmd_achats,reference',
                'numBordereauLivraison' => 'required|string|max:255|unique:reception_cmd_achats,numBordereauLivraison',
                'idExercice' => 'required|exists:exercices,idExercice',
                'idCommande' => 'required|exists:commande_achats,idCommande',
                'lignes.*.idDetailCom' => 'required|exists:detail_commande_achats,idDetailCom',
                'lignes.*.qteReceptionne' => 'required|numeric|min:1',
                'lignes.*.prixUnit' => 'required|numeric|min:0',
            ], [
                'dateC.before_or_equal' => 'La date de réception ne peut pas être future',
                'referenceC.unique' => 'Cette référence de BL existe déjà',
                'numBordereauLivraison.unique' => 'Ce numéro de bordereau existe déjà',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();

            // Vérification des quantités
            foreach ($validated['lignes'] as $ligne) {
                $detailCommande = DetailCommandeAchat::find($ligne['idDetailCom']);
                if ($ligne['qteReceptionne'] > $detailCommande->qteRestante) {
                    return back()->withErrors(['lignes.' . $ligne['idDetailCom'] . '.qteReceptionne' => 'La quantité reçue ne peut pas dépasser la quantité restante.'])->withInput();
                }
            }

            // Création de la réception
            $reception = ReceptionCmdAchat::create([
                'date' => $validated['dateC'],
                'reference' => $validated['referenceC'],
                'numBordereauLivraison' => $validated['numBordereauLivraison'],
                'statutRecep' => 'en cours',
                'idExercice' => $validated['idExercice'],
                'idCommande' => $validated['idCommande'],
                'idU' => auth()->id(),
            ]);

            // Création des lignes de réception
            foreach ($validated['lignes'] as $ligne) {
                if ($ligne['qteReceptionne'] > 0) {
                    DetailReceptionCmdAchat::create([
                        'idDetailCom' => $ligne['idDetailCom'],
                        'qteReceptionne' => $ligne['qteReceptionne'],
                        'prixUnit' => $ligne['prixUnit'],
                        'idRecep' => $reception->idRecep,
                    ]);
                }
            }

            // Vérification si toutes les lignes de commande ont été réceptionnées
            $commande = CommandeAchat::find($validated['idCommande']);
            $allLignesReceptionnees = $commande->lignes->every(function ($ligne) {
                return $ligne->qteRestante <= 0;
            });

            if ($allLignesReceptionnees) {
                $reception->update(['statutRecep' => 'complète']);
                $commande->update(['statutCom' => 'réceptionnée']);
            }

            DB::commit();
            return redirect()->route('receptions.index')->with('success', 'Réception créée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création de la réception: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $reception = ReceptionCmdAchat::with(['commandeAchat.lignes.produit', 'detailReceptionCmdAchat'])
            ->findOrFail($id);

        if ($reception->statutRecep === 'complète') {
            return redirect()->route('receptions.index')->with('error', 'Impossible de modifier une réception complète.');
        }

        $magasins = Magasin::all();
        $exercices = Exercice::where('statutExercice', 'actif')->get();

        return view('pages.Fournisseur&Achat.edit_reception', compact('reception', 'magasins', 'exercices'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $reception = ReceptionCmdAchat::findOrFail($id);

            if ($reception->statutRecep === 'complète') {
                return redirect()->route('receptions.index')->with('error', 'Impossible de modifier une réception complète.');
            }

            $validator = Validator::make($request->all(), [
                'dateC' => 'required|date|before_or_equal:today',
                'referenceC' => 'required|string|max:255|unique:reception_cmd_achats,reference,' . $id . ',idRecep',
                'numBordereauLivraison' => 'required|string|max:255|unique:reception_cmd_achats,numBordereauLivraison,' . $id . ',idRecep',
                'lignes.*.idDetailRecepCmdAchat' => 'required|exists:detail_reception_cmd_achats,idDetailRecepCmdAchat',
                'lignes.*.qteReceptionne' => 'required|numeric|min:1',
                'lignes.*.prixUnit' => 'required|numeric|min:0',
            ], [
                'dateC.before_or_equal' => 'La date de réception ne peut pas être future',
                'referenceC.unique' => 'Cette référence de BL existe déjà',
                'numBordereauLivraison.unique' => 'Ce numéro de bordereau existe déjà',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();

            // Mise à jour de la réception
            $reception->update([
                'date' => $validated['dateC'],
                'reference' => $validated['referenceC'],
                'numBordereauLivraison' => $validated['numBordereauLivraison'],
            ]);

            // Mise à jour des lignes de réception
            foreach ($validated['lignes'] as $ligne) {
                $detailReception = DetailReceptionCmdAchat::find($ligne['idDetailRecepCmdAchat']);
                $oldQte = $detailReception->qteReceptionne;
                $detailCommande = $detailReception->detailCommandeAchat;

                // Vérification de la quantité
                if ($ligne['qteReceptionne'] > ($detailCommande->qteRestante + $oldQte)) {
                    return back()->withErrors(['lignes.' . $ligne['idDetailRecepCmdAchat'] . '.qteReceptionne' => 'La quantité reçue ne peut pas dépasser la quantité restante.'])->withInput();
                }

                $detailReception->update([
                    'prixUnit' => $ligne['prixUnit'],
                ]);

                // Mise à jour de la quantité restante dans la ligne de commande
                $detailCommande->update([
                    'qteRecue' => $detailCommande->qteRecue - $oldQte + $ligne['qteReceptionne']
                ]);
            }

            // Vérification si toutes les lignes de commande ont été réceptionnées
            $commande = $reception->commandeAchat;
            $allLignesReceptionnees = $commande->lignes->every(function ($ligne) {
                return $ligne->qteRestante <= 0;
            });

            if ($allLignesReceptionnees) {
                $reception->update(['statutRecep' => 'complète']);
                $commande->update(['statutCom' => 'réceptionnée']);
            } else {
                $reception->update(['statutRecep' => 'en cours']);
                $commande->update(['statutCom' => 'validée']);
            }

            DB::commit();
            return redirect()->route('receptions.index')->with('success', 'Réception mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour de la réception: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $reception = ReceptionCmdAchat::findOrFail($id);

            if ($reception->statutRecep === 'complète') {
                return redirect()->route('receptions.index')->with('error', 'Impossible de supprimer une réception complète.');
            }

            // Restauration des quantités restantes dans les lignes de commande
            foreach ($reception->detailReceptionCmdAchat as $detail) {
                $detailCommande = $detail->detailCommandeAchat;
                $detailCommande->update([
                    'qteRecue' => $detailCommande->qteRecue - $detail->qteReceptionne
                ]);
            }

            // Suppression des lignes de réception
            $reception->detailReceptionCmdAchat()->delete();

            // Suppression de la réception
            $reception->delete();

            // Mise à jour du statut de la commande
            $commande = $reception->commandeAchat;
            $commande->update(['statutCom' => 'validée']);

            DB::commit();
            return redirect()->route('receptions.index')->with('success', 'Réception supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression de la réception: ' . $e->getMessage());
        }
    }
}