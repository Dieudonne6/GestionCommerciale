<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fournisseur;
use App\Http\Requests\FournisseurRequest;


class FournisseurController extends Controller
{

    public function fournisseur(){
        $allfournisseurs = Fournisseur::get();
        return view('pages.definition.fournisseur', compact('allfournisseurs'));
    }

    // creation fournisseur

    public function ajouterFournisseur( FournisseurRequest $request ) {

        $request->validated();

        

        // Vérifier si le fournisseur existe déjà
        $fournisseurExiste = Fournisseur::where('identiteF', $request->input('identiteF'))
        ->exists();

        if ($fournisseurExiste) {
            // Retourner une erreur si le fournisseur existe déjà
            return back()->with(['erreur' => 'Ce fournisseur existe déjà.']);
        }


        try {
            // Votre logique pour ajouter un fournisseur

             // creer un nouveau fournisseur dans le cas echeant
            $Fournisseur = new Fournisseur();
            $Fournisseur->identiteF = $request->input('identiteF');
            // $Fournisseur->PrenomF = $request->input('PrenomF');
            $Fournisseur->AdresseF = $request->input('AdresseF');
            $Fournisseur->ContactF = $request->input('ContactF');
            $Fournisseur->save();

            return back()->with("status", "Le fournisseur a ete creer avec succes");

        } catch (\Exception $e) {
            // Stockez l'ID du modal d'ajout en cas d'erreur
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->with('errorModalId', 'addBoardModal'); // ID du modal d'ajout
        }

       
    }


    // suppression fournisseur

    public function deleteFournisseur ($id) {
        $fournisseur = Fournisseur::where('idF', $id)->first();
        $fournisseur->delete();
        return back()->with("status", "Le fournisseur a ete supprimer avec succes");
    }


    // modification fournisseur

    public function updateFournisseur ( FournisseurRequest $request, $id ) {

        $request->validated();


        try {
            // Votre logique pour modifier le fournisseur

            $modifFournisseur = Fournisseur::where('idF', $id)->first();
            $modifFournisseur->identiteF = $request->input('identiteF');
            // $modifFournisseur->PrenomF = $request->input('PrenomF');
            $modifFournisseur->AdresseF = $request->input('AdresseF');
            $modifFournisseur->ContactF = $request->input('ContactF');
            $modifFournisseur->update();  
            return back()->with("status", "Le fournisseur a ete modifier avec succes");

        } catch (\Exception $e) {
            // Stockez l'ID du modal dans la session en cas d'erreur
            return redirect()->back()
            ->withErrors($e->getMessage())
            ->with('errorModalId', 'ModifyBoardModal' . $id); // ID dynamique du modal de modification
        }


  
    }




}
