<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fournisseur;
use App\Models\Produit;
use App\Models\CommandeAchat;
use App\Models\DetailCommandeAchat;
use Illuminate\Support\Facades\DB;

class ApprovisionnementController extends Controller
{
    //

    public function commandeAchat()
    {
        $allfournisseurs = Fournisseur::get();
        $allproduits = Produit::get();
        $allcommande = CommandeAchat::with('fournisseur', 'detailCommandeAchat')->get();
        return view('pages.Fournisseur&Achat.commandeAchat', compact('allfournisseurs', 'allproduits', 'allcommande'));
    }
    public function reception()
    {
        return view('pages.Fournisseur&Achat.reception');
    }
    public function ajoutercommande()
    {
        $allfournisseurs = Fournisseur::get();
        $allproduits = Produit::get();

        return view('pages.Fournisseur&Achat.ajoutercommande', compact('allfournisseurs', 'allproduits'));
    }
    public function ajouterLignCmd(Request $request)
    {
        // Validation des données
        $request->validate([
            'idP' => 'required|exists:produits,idP',
            'quantity' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
        ]);


        // Traitement des données
        $line = [
            'idP' => $request->idP,
            'qteCmd' => $request->quantity,
            'prix' => $request->price
            // 'tva' => $request->price * 0.2, 
            // 'ttc' => $request->price * 1.2
        ];

        // Retour en JSON
        return response()->json([
            'success' => true,
            'line' => $line
        ]);
    }



    public function ajouterLigneCommande(Request $request)
    {
        $validated = $request->validate([
            'idP' => 'required|exists:produits,idP',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'tva' => 'required|numeric|min:0',
        ]);

        $produit = Produit::where('idP', $validated['idP'])->first();
        $nomProduit = $produit->NomP;
        $montantHT = $validated['quantity'] * $validated['price'];
        $montantTTC = $validated['quantity'] * $validated['price'] * $validated['tva'];

        $ligneCommande = DetailCommandeAchat::create([
            'qteCmd' => $validated['quantity'],
            'prixUnit' => $validated['price'],
            'montantHT' => $montantHT,
            'montantTTC' => $montantTTC,
            'TVA' => $validated['tva'],
            'idPro' => $validated['idP'],
        ]);

        $donneLigneCommande = [
            'produit' => $nomProduit,
            'quantite' => $validated['quantity'],
            'montant_ht' => $montantHT,
            'tva' => $validated['tva'],
            'montant_ttc' => $montantTTC,
        ];

        return back()->with('donneLigneCommande', $donneLigneCommande);
    }

    public function storeCommande(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validation des données
            $validated = $request->validate([
                'dateOp' => 'required|date',
                'reference' => 'required|string|max:255',
                'delailivraison' => 'required|string|max:255',
                'idF' => 'required|exists:fournisseurs,idF',
                'idExercice' => 'required|exists:exercices,idExercice',
                'idE' => 'required|exists:entreprises,idE',
                'idU' => 'required|exists:utilisateurs,idU',
                'lignes.*.idP' => 'required|exists:produits,idP',
                'lignes.*.qteCmd' => 'required|integer|min:1',
                'lignes.*.prixUnit' => 'required|numeric|min:0',
                'lignes.*.TVA' => 'required|numeric|min:0',
            ]);

            // Création de la commande
            $commande = CommandeAchat::create([
                'dateOp' => $validated['dateOp'],
                'reference' => $validated['reference'],
                'delailivraison' => $validated['delailivraison'],
                'statutCom' => 'En cours',
                'idF' => $validated['idF'],
                'idExercice' => $validated['idExercice'],
                'idE' => $validated['idE'],
                'idU' => $validated['idU'],
            ]);

            // Calcul des montants totaux
            $montantTotalHT = 0;
            $montantTotalTTC = 0;

            // Création des lignes de commande
            foreach ($validated['lignes'] as $ligne) {
                $montantHT = $ligne['qteCmd'] * $ligne['prixUnit'];
                $montantTTC = $montantHT * (1 + $ligne['TVA'] / 100);

                DetailCommandeAchat::create([
                    'qteCmd' => $ligne['qteCmd'],
                    'prixUnit' => $ligne['prixUnit'],
                    'montantHT' => $montantHT,
                    'montantTTC' => $montantTTC,
                    'TVA' => $ligne['TVA'],
                    'idPro' => $ligne['idP'],
                    'idCommande' => $commande->idCommande,
                ]);

                $montantTotalHT += $montantHT;
                $montantTotalTTC += $montantTTC;
            }

            // Mise à jour des montants totaux de la commande
            $commande->update([
                'montantTotalHT' => $montantTotalHT,
                'montantTotalTTC' => $montantTotalTTC,
            ]);

            DB::commit();
            return redirect()->route('commande.achat')->with('success', 'Commande créée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création de la commande: ' . $e->getMessage());
        }
    }

    public function updateCommande(Request $request, $idCommande)
    {
        DB::beginTransaction();
        try {
            $commande = CommandeAchat::findOrFail($idCommande);

            // Validation des données
            $validated = $request->validate([
                'dateOp' => 'required|date',
                'reference' => 'required|string|max:255',
                'delailivraison' => 'required|string|max:255',
                'idF' => 'required|exists:fournisseurs,idF',
                'lignes.*.idP' => 'required|exists:produits,idP',
                'lignes.*.qteCmd' => 'required|integer|min:1',
                'lignes.*.prixUnit' => 'required|numeric|min:0',
                'lignes.*.TVA' => 'required|numeric|min:0',
            ]);

            // Mise à jour de la commande
            $commande->update([
                'dateOp' => $validated['dateOp'],
                'reference' => $validated['reference'],
                'delailivraison' => $validated['delailivraison'],
                'idF' => $validated['idF'],
            ]);

            // Suppression des anciennes lignes
            DetailCommandeAchat::where('idCommande', $commande->idCommande)->delete();

            // Calcul des montants totaux
            $montantTotalHT = 0;
            $montantTotalTTC = 0;

            // Création des nouvelles lignes
            foreach ($validated['lignes'] as $ligne) {
                $montantHT = $ligne['qteCmd'] * $ligne['prixUnit'];
                $montantTTC = $montantHT * (1 + $ligne['TVA'] / 100);

                DetailCommandeAchat::create([
                    'qteCmd' => $ligne['qteCmd'],
                    'prixUnit' => $ligne['prixUnit'],
                    'montantHT' => $montantHT,
                    'montantTTC' => $montantTTC,
                    'TVA' => $ligne['TVA'],
                    'idPro' => $ligne['idP'],
                    'idCommande' => $commande->idCommande,
                ]);

                $montantTotalHT += $montantHT;
                $montantTotalTTC += $montantTTC;
            }

            // Mise à jour des montants totaux
            $commande->update([
                'montantTotalHT' => $montantTotalHT,
                'montantTotalTTC' => $montantTotalTTC,
            ]);

            DB::commit();
            return redirect()->route('commande.achat')->with('success', 'Commande mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour de la commande: ' . $e->getMessage());
        }
    }

    public function destroyCommande($idCommande)
    {
        DB::beginTransaction();
        try {
            $commande = CommandeAchat::findOrFail($idCommande);

            // Suppression des lignes de commande
            DetailCommandeAchat::where('idCommande', $commande->idCommande)->delete();

            // Suppression de la commande
            $commande->delete();

            DB::commit();
            return redirect()->route('commande.achat')->with('success', 'Commande supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression de la commande: ' . $e->getMessage());
        }
    }

    public function deleteLigneCommande($idDetailCom)
    {
        DB::beginTransaction();
        try {
            $ligne = DetailCommandeAchat::findOrFail($idDetailCom);
            $commande = $ligne->commandeAchat;

            // Suppression de la ligne
            $ligne->delete();

            // Recalcul des montants totaux
            $montantTotalHT = DetailCommandeAchat::where('idCommande', $commande->idCommande)
                ->sum('montantHT');
            $montantTotalTTC = DetailCommandeAchat::where('idCommande', $commande->idCommande)
                ->sum('montantTTC');

            // Mise à jour des montants totaux
            $commande->update([
                'montantTotalHT' => $montantTotalHT,
                'montantTotalTTC' => $montantTotalTTC,
            ]);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
