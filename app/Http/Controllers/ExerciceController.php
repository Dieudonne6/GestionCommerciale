<?php

namespace App\Http\Controllers;
use App\Models\Exercice;
use Illuminate\Http\Request;

class ExerciceController extends Controller
{
    public function exercice () {
        $exercices = Exercice::all();
        $exerciceAct =Exercice::where('statut', 1)->first();
        $exerciceActif = $exerciceAct->annee;

        return view ('pages.parametres.exercice')->with('exercices', $exercices)->with('exerciceActif', $exerciceActif);
    }


    public function ajouterExercice (Request $request) {


        // Vérifier si l'exercice existe déjà
        $exerciceExiste = Exercice::where('annee', $request->input('annee'))
        ->exists();

        if ($exerciceExiste) {
            // Retourner une erreur si l'exercice existe déjà
            return back()->with(['erreur' => 'Cet exercice existe déjà.']);
        }

        // Mettre à jour le statut de tous les autres exercices à 0
        Exercice::query()->update(['statut' => 0]);

        // Ajout du nouveau exercice
        $exercice = new Exercice();
        $exercice->annee = $request->input('annee');
        $exercice->statut = 1;
        $exercice->dateDebut = $request->input('dateDebut');
        $exercice->dateFin = $request->input('dateFin');
        $exercice->save();

        return back()->with("status", "L'exercice a ete creer avec succes");

    }

    public function activerExercice($id) {

            // Mettre à jour le statut de tous les autres exercices à 0
            Exercice::query()->update(['statut' => 0]);

            $exerciceSpecifique = Exercice::where('idE', $id)->first();
            $exerciceSpecifique->statut = 1;
            $exerciceSpecifique->update();

            return back()->with("status", "L'exercice a ete activé avec succes");


    }
}
