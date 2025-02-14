<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategorieProduit;
use App\Http\Requests\CategorieProduitRequest;

class CategorieProduitController extends Controller
{

    public function categorieProduit(){
        $allCategorieProduits = CategorieProduit::get();
        return view('pages.ProduitStock.categorieProduit', compact('allCategorieProduits'));
    }


    public function ajouterCategorieProduit( CategorieProduitRequest $request ) {

        $request->validated();

        

        $categorieProduitExiste = CategorieProduit::where('libelle', $request->input('libelle'))
        ->exists();

        if ($categorieProduitExiste) {
            return back()->with(['erreur' => 'Cette categorie de produit existe déjà.']);
        }


        try {
            $CategorieProduit = new CategorieProduit();
            $CategorieProduit->libelle = $request->input('libelle');
            $CategorieProduit->codeCatPro = $request->input('codeCatPro');
            $CategorieProduit->save();

            return back()->with("status", "La categorie de produit a été creer avec succes");

        } catch (\Exception $e) {
            // Stockez l'ID du modal d'ajout en cas d'erreur
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->with('errorModalId', 'addBoardModal'); // ID du modal d'ajout
        }

       
    }



    public function supprimerCategorieProduit ($idCatPro) {
        $CategorieProduit = CategorieProduit::where('idCatPro', $idCatPro)->first();
        $CategorieProduit->delete();
        return back()->with("status", "La categorie de produit a été supprimer avec succes");
    }



    public function modifierCategorieProduit ( CategorieProduitRequest $request, $idCatPro ) {

        $request->validated();


        try {

            $modifCategorieProduit = CategorieProduit::where('idCatPro', $idCatPro)->first();
            $modifCategorieProduit->libelle = $request->input('libelle');
            $modifCategorieProduit->codeCatPro = $request->input('codeCatPro');
            $modifCategorieProduit->update();  
            return back()->with("status", "La categorie de produit a été modifier avec succes");

        } catch (\Exception $e) {
            // Stockez l'ID du modal dans la session en cas d'erreur
            return redirect()->back()
            ->withErrors($e->getMessage())
            ->with('errorModalId', 'ModifyBoardModal' . $idCatPro); // ID dynamique du modal de modification
        }


  
    }




}
