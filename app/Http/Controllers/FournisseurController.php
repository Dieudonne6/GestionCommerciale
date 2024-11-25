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

        // Vérifier si le fournisseur existe déjà
        $fournisseurExiste = Fournisseur::where('NomF', $request->input('NomF'))
        ->where('PrenomF', $request->input('PrenomF'))
        ->exists();

        if ($fournisseurExiste) {
            // Retourner une erreur si le fournisseur existe déjà
            return back()->with(['erreur' => 'Ce fournisseur existe déjà.']);
        }

        // creer un nouveau fournisseur dans le cas echeant
        $Fournisseur = new Fournisseur();
        $Fournisseur->NomF = $request->input('NomF');
        $Fournisseur->PrenomF = $request->input('PrenomF');
        $Fournisseur->AdresseF = $request->input('AdresseF');
        $Fournisseur->ContactF = $request->input('ContactF');
        $Fournisseur->save();

        return back()->with("status", "Le fournisseur a ete creer avec succes");
    }


    // suppression fournisseur

    public function deleteFournisseur ($id) {
        $fournisseur = Fournisseur::where('idF', $id)->first();
        $fournisseur->delete();
        return back()->with("status", "Le fournisseur a ete supprimer avec succes");
    }


    // modification fournisseur

    public function updateFournisseur ( FournisseurRequest $request, $id ) {
        $modifFournisseur = Fournisseur::where('idF', $id)->first();
        $modifFournisseur->NomF = $request->input('NomF');
        $modifFournisseur->PrenomF = $request->input('PrenomF');
        $modifFournisseur->AdresseF = $request->input('AdresseF');
        $modifFournisseur->ContactF = $request->input('ContactF');
        $modifFournisseur->update();    
    }




}
