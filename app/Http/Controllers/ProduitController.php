<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\CategorieProduit;
use App\Models\FamilleProduit;
use App\Http\Requests\ProduitRequest;

class ProduitController extends Controller
{
    public function Produits(){
        // Charger tous les produits avec leur quantité stockée associée
        $allProduits = Produit::with('stocke')->get();
        $allCategorieProduits = CategorieProduit::get();
        $allFamilleProduits = FamilleProduit::get();
        return view('pages.ProduitStock.produit', compact('allProduits', 'allCategorieProduits', 'allFamilleProduits'));
    }   

    public function ajouterProduit( ProduitRequest $request ) {

        $request->validated();

        

        $ProduitExiste = Produit::where('libelle', $request->input('libelle'))
        ->exists();

        if ($ProduitExiste) {
            return back()->with(['erreur' => 'Ce produit existe déjà.']);
        }


        try {
            $Produit = new Produit();
            $Produit->libelle = $request->input('libelle');
            $Produit->idCatPro = $request->input('idCatPro');
            $Produit->idFamPro = $request->input('idFamPro');
            $Produit->prix = $request->input('prix');
            $Produit->desc = $request->input('desc');
            $imageContent = file_get_contents($request->file('image')->getRealPath());
            $Produit->image = $imageContent;
            $Produit->save();

            return back()->with("status", "Le produit a été creer avec succes");

        } catch (\Exception $e) {
            // Stockez l'ID du modal d'ajout en cas d'erreur
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



    public function modifierProduit ( ProduitRequest $request, $idPro ) {

        $request->validated();


        try {

            $modifProduit = Produit::where('idPro', $idPro)->first();
            $modifProduit->libelle = $request->input('libelle');
            $modifProduit->idCatPro = $request->input('idCatPro');
            $modifProduit->idFamPro = $request->input('idFamPro');
            $modifProduit->prix = $request->input('prix');
            $modifProduit->desc = $request->input('desc');
            $modifProduit->image = file_get_contents($request->file('image')->getRealPath());
            $modifProduit->update();  
            return back()->with("status", "Le produit a été modifier avec succes");

        } catch (\Exception $e) {
            // Stockez l'ID du modal dans la session en cas d'erreur
            return redirect()->back()
            ->withErrors($e->getMessage())
            ->with('errorModalId', 'ModifyBoardModal' . $idCatPro); // ID dynamique du modal de modification
        }


  
    }

}
