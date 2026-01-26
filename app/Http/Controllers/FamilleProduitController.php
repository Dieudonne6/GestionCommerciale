<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FamilleProduit;
use App\Http\Requests\FamilleProduitRequest;

class FamilleProduitController extends Controller
{

    public function familleProduit(){
        $user = auth()->user();
        $userId = $user->idU;
        $entrepriseId = $user->idE;
        $entreprise = $user->entreprise;
        $regimeEntreprise = $entreprise->regime;

        // dd($regimeEntreprise);

        $allFamilleProduits = FamilleProduit::get();
        return view('pages.ProduitStock.familleProduit', compact('allFamilleProduits', 'regimeEntreprise'));
    }


    public function ajouterFamilleProduit( FamilleProduitRequest $request ) {

        // dd($request->all());

        $request->validated();

        

        $familleProduitExiste = FamilleProduit::where('codeFamille', $request->input('codeFamille'))
        ->orWhere('libelle', $request->input('libelle'))
        ->exists();

        if ($familleProduitExiste) {
            return back()->with(['erreur' => 'Cette famille de produit existe déjà.']);
        }


        try {
            $FamilleProduit = new FamilleProduit();
            $FamilleProduit->codeFamille = $request->input('codeFamille');
            $FamilleProduit->libelle = $request->input('libelle');
            $FamilleProduit->TVA = $request->input('TVA');
            $FamilleProduit->groupe = $request->input('groupe');
            $FamilleProduit->save();

            return back()->with("status", "La famille de produit a été creer avec succes");

        } catch (\Exception $e) {
            // Stockez l'ID du modal d'ajout en cas d'erreur
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->with('errorModalId', 'addBoardModal'); // ID du modal d'ajout
        }

       
    }



    public function supprimerFamilleProduit ($idFamPro) {
        $FamilleProduit = FamilleProduit::where('idFamPro', $idFamPro)->first();
        $FamilleProduit->delete();
        return back()->with("status", "La famille de produit a été supprimer avec succes");
    }



    public function modifierFamilleProduit ( FamilleProduitRequest $request, $idFamPro ) {

        $request->validated();


        try {

            $modifFamilleProduit = FamilleProduit::where('idFamPro', $idFamPro)->first();
            $modifFamilleProduit->codeFamille = $request->input('codeFamille');
            $modifFamilleProduit->libelle = $request->input('libelle');
            $modifFamilleProduit->TVA = $request->input('TVA');
            $modifFamilleProduit->update();  
            return back()->with("status", "La famille de produit a été modifier avec succes");

        } catch (\Exception $e) {
            // Stockez l'ID du modal dans la session en cas d'erreur
            return redirect()->back()
            ->withErrors($e->getMessage())
            ->with('errorModalId', 'ModifyBoardModal' . $id); // ID dynamique du modal de modification
        }


  
    }




}
