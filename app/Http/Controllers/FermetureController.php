<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fermetures;
use App\Models\DetailFermetures;
use App\Models\Stocke;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FermetureController extends Controller
{
    public function store(Request $request)
    {
        $date = now()->toDateString();

        // Vérifier si la fermeture existe déjà
        // $existe = Fermetures::where('date', $date)->first();

        // if ($existe) {
        //     return back()->with('error', 'La fermeture de cette journée est déjà effectuée');
        // }

        DB::transaction(function () use ($date) {

            $fermeture = Fermetures::create([
                'idU'  => Auth::id(),
                'date' => $date,
                'heure'=> now()->toTimeString(),
            ]);

            $stocks = Stocke::all();

            foreach ($stocks as $stock) {
                DetailFermetures::create([
                    'idFermeture' => $fermeture->idFermeture,
                    'idPro'       => $stock->idPro,
                    'qteStocke'   => $stock->qteStocke,
                ]);
            }
        });

        return back()->with('success', 'Fermeture de la journée effectuée');
    }
}
