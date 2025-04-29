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

class ReceptionCmdAchatController extends Controller
{
    public function index()
    {
        $receptions = ReceptionCmdAchat::with(['commandeAchat.fournisseur', 'detailReceptionCmdAchat.detailCommandeAchat.produit'])
            ->orderBy('date', 'desc')
            ->get();

        $commandes = CommandeAchat::where('statutCom', 'validée')
            ->whereDoesntHave('receptionCmdAchat', function ($q) {
                $q->where('statutRecep', 'complète');
            })
            ->with(['detailCommandeAchat.produit' => function ($q) {
                $q->where('qteRestante', '>', 0);
            }])
            ->get();

        $magasins = Magasin::all();
        $exercices = Exercice::where('statutExercice', 'actif')->get();

        return view('pages.Fournisseur&Achat.gestion_receptions', compact(
            'receptions',
            'commandes',
            'magasins',
            'exercices'
        ));
    }

    public function getCommandeDetails($idCommande)
    {
        $commande = CommandeAchat::with(['detailCommandeAchat.produit' => function ($q) {
            $q->where('qteRestante', '>', 0);
        }])->findOrFail($idCommande);

        return response()->json([
            'success' => true,
            'details' => $commande->detailCommandeAchat->map(function ($detail) {
                return [
                    'idDetailCom' => $detail->idDetailCom,
                    'produit' => $detail->produit->libelle,
                    'qteRestante' => $detail->qteRestante,
                    'prixUnit' => $detail->prixUnit
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'reference' => 'required|string|max:255|unique:reception_cmd_achats,reference',
            'numBordereauLivraison' => 'required|string|max:255|unique:reception_cmd_achats,numBordereauLivraison',
            'idExercice' => 'required|exists:exercices,idExercice',
            'idCommande' => 'required|exists:commande_achats,idCommande',
            'details.*.idDetailCom' => 'required|exists:detail_commande_achats,idDetailCom',
            'details.*.qteReceptionne' => 'required|numeric|min:1',
            'details.*.prixUnit' => 'required|numeric|min:0',
        ], [
            'date.before_or_equal' => 'La date de réception ne peut pas être future',
            'reference.unique' => 'Cette référence de BL existe déjà',
            'numBordereauLivraison.unique' => 'Ce numéro de bordereau existe déjà',
            'details.*.qteReceptionne.min' => 'La quantité doit être supérieure à 0',
            'details.*.prixUnit.min' => 'Le prix unitaire doit être positif',
        ]);

        DB::transaction(function () use ($data) {
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
                $dc = DetailCommandeAchat::findOrFail($d['idDetailCom']);

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
                ]);

                // Mise à jour des quantités
                $dc->decrement('qteRestante', $d['qteReceptionne']);
                $dc->increment('qteRecue', $d['qteReceptionne']);
            }

            // Mise à jour du statut
            $commande = CommandeAchat::findOrFail($data['idCommande']);
            if ($commande->detailCommandeAchat->every(fn($l) => $l->qteRestante <= 0)) {
                $reception->update(['statutRecep' => 'complète']);
                $commande->update(['statutCom' => 'réceptionnée']);
            }
        });

        return redirect()->route('receptions.index')
            ->with('status', 'Réception créée avec succès.');
    }

    public function update(Request $request, $id)
    {
        $reception = ReceptionCmdAchat::with('detailReceptionCmdAchat')->findOrFail($id);

        if ($reception->statutRecep === 'complète') {
            return redirect()->route('receptions.index')
                ->with('erreur', 'Impossible de modifier une réception complète.');
        }

        $data = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'reference' => 'required|string|max:255|unique:reception_cmd_achats,reference,' . $id . ',idRecep',
            'numBordereauLivraison' => 'required|string|max:255|unique:reception_cmd_achats,numBordereauLivraison,' . $id . ',idRecep',
            'details.*.idDetailCom' => 'required|exists:detail_commande_achats,idDetailCom',
            'details.*.qteReceptionne' => 'required|numeric|min:1',
            'details.*.prixUnit' => 'required|numeric|min:0',
        ], [
            'date.before_or_equal' => 'La date de réception ne peut pas être future',
            'reference.unique' => 'Cette référence de BL existe déjà',
            'numBordereauLivraison.unique' => 'Ce numéro de bordereau existe déjà',
        ]);

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
                $dc = DetailCommandeAchat::findOrFail($d['idDetailCom']);

                if ($d['qteReceptionne'] > $dc->qteRestante) {
                    abort(422, 'Qté reçue > qt restante pour produit ' . $dc->produit->libelle);
                }

                DetailReceptionCmdAchat::create([
                    'idRecep' => $reception->idRecep,
                    'idDetailCom' => $d['idDetailCom'],
                    'qteReceptionne' => $d['qteReceptionne'],
                    'prixUnit' => $d['prixUnit'],
                ]);

                $dc->decrement('qteRestante', $d['qteReceptionne']);
                $dc->increment('qteRecue', $d['qteReceptionne']);
            }

            // Mise à jour statuts
            $cmd = $reception->commandeAchat;
            if ($cmd->detailCommandeAchat->every(fn($l) => $l->qteRestante <= 0)) {
                $reception->update(['statutRecep' => 'complète']);
                $cmd->update(['statutCom' => 'réceptionnée']);
            } else {
                $reception->update(['statutRecep' => 'en cours']);
                $cmd->update(['statutCom' => 'validée']);
            }
        });

        return redirect()->route('receptions.index')
            ->with('status', 'Réception mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $reception = ReceptionCmdAchat::findOrFail($id);

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

            // Réinitialiser statut commande
            $reception->commandeAchat->update(['statutCom' => 'validée']);
        });

        return redirect()->route('receptions.index')
            ->with('status', 'Réception supprimée avec succès.');
    }
}
