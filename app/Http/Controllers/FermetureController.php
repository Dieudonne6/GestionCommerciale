<?php

namespace App\Http\Controllers;

use App\Models\Fermetures;
use App\Models\Stocke;
use Illuminate\Support\Facades\Auth;

class FermetureController extends Controller
{
    public function store()
    {
        $stocks = Stocke::all();

        foreach ($stocks as $stock) {
            Fermetures::create([
                'idPro'    => $stock->idPro,
                'qtestock' => $stock->qteStocke,
                'idU'      => Auth::id(),
                'date'     => now()->toDateString(),
                'heure'    => now()->toTimeString(),
            ]);
        }

        return redirect()->back()->with('success', 'Fermeture de la journée effectuée');
    }
}
