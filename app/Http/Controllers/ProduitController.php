<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\CategorieProduit;
use App\Models\FamilleProduit;
use App\Models\Magasin;
use App\Http\Requests\ProduitRequest;
use App\Models\Stocke;

class ProduitController extends Controller
{
    public function Produits(){
        // Charger tous les produits avec leur quantité stockée associée
        $allProduits = Produit::with('stocke')->get();
        $allCategorieProduits = CategorieProduit::get();
        $allFamilleProduits = FamilleProduit::get();

        $user = auth()->user();
        $userId = $user->idU;
        $entrepriseId = $user->idE;
        // dd($entrepriseId);
        $magasins = Magasin::where('idE', $entrepriseId)
            ->with('entreprise')
            ->get();

        // $magasins = Magasin::get();
        return view('pages.ProduitStock.produit', compact('allProduits', 'allCategorieProduits', 'allFamilleProduits', 'magasins'));
    }   

public function ajouterProduit(ProduitRequest $request)
{
    $request->validated();
    // $ProduitExiste = Produit::where('libelle', $request->input('libelle'))->exists();
    // if ($ProduitExiste) {
    //     return back()->with(['erreur' => 'Ce produit existe déjà.']);
    // }
    try {
        $Produit = new Produit();
        $Produit->libelle = $request->input('libelle');
        $Produit->idCatPro = $request->input('idCatPro') ;
        $Produit->idFamPro = $request->input('idFamPro') ;
        /* $Produit->idMag = $request->input('idMag'); */
        $Produit->prix = $request->input('prix');
          $Produit->desc = $request->input('desc');
        $Produit->prixAchatTheorique = $request->input('prixAchat');
        $Produit->marge = $request->input('marge');
        $Produit->stockAlert = $request->input('stockAlert');
        $Produit->stockMinimum = $request->input('stockMinimum');
        $imageContent = file_get_contents($request->file('image')->getRealPath());
        $Produit->image = $imageContent;
        $Produit->prixReelAchat = $request->input('prixReelAchat');
        $Produit->save();

            // Création de l'entrée dans la table Stockes
            Stocke::create([
                'qteStocke' => $request->input('qteStocke'),
                'CUMP' => 0, // Coût unitaire moyen pondéré initial
                'idPro' => $Produit->idPro, // ID du produit créé
                'idMag' => $request->input('idMag'), // ID du magasin
            ]);

            return back()->with("status", "Le produit a été créé avec succès");
            // Nettoyer la session au cas où il resterait une ancienne erreur
            session()->forget('errorModalId');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->with('errorModalId', 'addBoardModal'); // ID du modal d'ajout
        }       
    }



    public function supprimerProduit ($idPro) {
        $Produit = Produit::where('idPro', $idPro)->first();
        $Produit->delete();
        return back()->with("status", "Le produit a été supprimer avec succes");
    }



    public function modifierProduit(ProduitRequest $request, $idPro)
    {
        $request->validated();
        try {
            // Mise à jour des infos du produit
            $modifProduit = Produit::where('idPro', $idPro)->first();
            $modifProduit->libelle = $request->input('libelle');
            $modifProduit->idCatPro = $request->input('idCatPro');
            $modifProduit->idFamPro = $request->input('idFamPro');
            $modifProduit->prixAchatTheorique = $request->input('prixAchat');
            $modifProduit->marge = $request->input('marge');
            $modifProduit->prix = $request->input('prix');
            $modifProduit->desc = $request->input('desc');
            $modifProduit->stockAlert = $request->input('stockAlert');
            $modifProduit->stockMinimum = $request->input('stockMinimum');
            $modifProduit->prixReelAchat = $request->input('prixReelAchat');
            
            // Mettre à jour l'image seulement si une nouvelle est fournie
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $modifProduit->image = file_get_contents($request->file('image')->getRealPath());
            }
            
            $modifProduit->update();
    
            // Mise à jour de la quantité stockée et du magasin
            $stocke = Stocke::where('idPro', $idPro)->first();
    
            if ($stocke) {
                $stocke->idMag = $request->input('idMag');
                // $stocke->qteStocke = $request->input('qteStocke');
                $stocke->update();
            }
    
            session()->forget('errorModalId');
            return back()->with("status", "Le produit a été modifié avec succès");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->with('errorModalId', 'ModifyBoardModal' . $idPro);
        }  
    }
    
        public function detail($idPro)
        {
            $produit = Produit::with([
                'categorieProduit',
                'familleProduit',
                'stocke.magasin',
                'detailCommandeAchat'
            ])->findOrFail($idPro);

            return view('pages.ProduitStock.detail', compact('produit'));
        }


}
