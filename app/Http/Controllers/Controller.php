<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Caise;
use Illuminate\Http\Request;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        $caisses = Caise::all();
        return view('pages.definition.caisses', compact('caisses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codeCais' => 'required|string|unique:caises,codeCais|max:255',
            'libelleCais' => 'required|string|max:255',
        ]);

        Caise::create([
            'codeCais' => $request->codeCais,
            'libelleCais' => $request->libelleCais,
        ]);

        return redirect()->back()->with('success', 'Caisse ajoutée avec succès.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'codeCais' => 'required|string|max:255|unique:caises,codeCais,' . $id . ',idCais',
            'libelleCais' => 'required|string|max:255',
        ]);

        $caisse = Caise::findOrFail($id);
        $caisse->update([
            'codeCais' => $request->codeCais,
            'libelleCais' => $request->libelleCais,
        ]);

        return redirect()->back()->with('success', 'Caisse modifiée avec succès.');
    }

    public function destroy($id)
    {
        $caisse = Caise::findOrFail($id);
        $caisse->delete();

        return redirect()->back()->with('success', 'Caisse supprimée avec succès.');
    }
}