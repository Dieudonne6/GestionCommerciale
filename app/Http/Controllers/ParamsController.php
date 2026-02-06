<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Params;

class ParamsController extends Controller
{
    public function index() {
        return view('pages.parametres.delaiAlert');
    }

    public function store(Request $request)
    {
        $request->validate([
            'delai_alerte' => 'required|integer|min:1',
        ]);

        \App\Models\Params::updateOrCreate(
            ['id' => 1],
            ['delai_alerte' => $request->delai_alerte]
        );

        return back()->with('success', 'Délai enregistré avec succès');
    }

}
