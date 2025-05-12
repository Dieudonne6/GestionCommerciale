<?php

namespace App\Http\Controllers;

use App\Models\Exercice;
use Illuminate\Http\Request;

class ExerciceController extends Controller
{
    public function exercice()
    {
        $exercices = Exercice::all();
        $exerciceAct = Exercice::where('statutExercice', 1)->first();
        $exerciceActif = $exerciceAct ? $exerciceAct->annee : null;

        return view('pages.parametres.exercice')->with('exercices', $exercices)->with('exerciceActif', $exerciceActif);
    }


    public function ajouterExercice(Request $request)
    {


        // Vérifier si l'exercice existe déjà
        $exerciceExiste = Exercice::where('annee', $request->input('annee'))
            ->exists();

        if ($exerciceExiste) {
            // Retourner une erreur si l'exercice existe déjà
            return back()->with(['erreur' => 'Cet exercice existe déjà.']);
        }

        // Mettre à jour le statut de tous les autres exercices à 0
        Exercice::query()->update(['statutExercice' => 0]);

        // Ajout du nouveau exercice
        $exercice = new Exercice();
        $exercice->annee = $request->input('annee');
        $exercice->statutExercice = 1;
        $exercice->dateDebut = $request->input('dateDebut');
        $exercice->dateFin = $request->input('dateFin');
        $exercice->save();

        return back()->with("status", "L'exercice a ete creer avec succes");
    }

    public function activerExercice($idExercice)
    {

        // Mettre à jour le statut de tous les autres exercices à 0
        Exercice::query()->update(['statutExercice' => 0]);

        $exerciceSpecifique = Exercice::where('idExercice', $idExercice)->first();
        $exerciceSpecifique->statutExercice = 1;
        $exerciceSpecifique->update();

        return back()->with("status", "L'exercice a ete activé avec succes");
    }
}