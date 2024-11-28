<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Caise;
use App\Models\Reception;
use App\Models\Commande; 
use App\Models\Magasin; 
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
        // Validation avec gestion des doublons
        $request->validate([
            'codeCais' => 'required|string|unique:caises,codeCais|max:255',
            'libelleCais' => 'required|string|max:255',
        ], [
            'codeCais.required' => 'Le code de la caisse est obligatoire.',
            'codeCais.unique' => 'Ce code existe déjà. Veuillez en saisir un autre.',
            'libelleCais.required' => 'Le libellé de la caisse est obligatoire.',
        ]);

        // Création de la caisse
        Caise::create([
            'codeCais' => $request->codeCais,
            'libelleCais' => $request->libelleCais,
        ]);

        return redirect()->back()->with('success', 'Caisse ajoutée avec succès.');
    }

    public function update(Request $request, $id)
    {
        // Validation avec gestion des doublons lors de la modification
        $request->validate([
            'codeCais' => 'required|string|max:255|unique:caises,codeCais,' . $id . ',idCais',
            'libelleCais' => 'required|string|max:255',
        ], [
            'codeCais.required' => 'Le code de la caisse est obligatoire.',
            'codeCais.unique' => 'Ce code existe déjà pour une autre caisse.',
            'libelleCais.required' => 'Le libellé de la caisse est obligatoire.',
        ]);

        // Mise à jour de la caisse
        $caisse = Caise::findOrFail($id);
        $caisse->update([
            'codeCais' => $request->codeCais,
            'libelleCais' => $request->libelleCais,
        ]);

        return redirect()->back()->with('success', 'Caisse modifiée avec succès.');
    }

    public function destroy($id)
    {
        // Suppression de la caisse
        $caisse = Caise::findOrFail($id);
        $caisse->delete();

        return redirect()->back()->with('success', 'Caisse supprimée avec succès.');
    }

    // Afficher la page de réception
    public function reception()
    {
        $receptions = Reception::with(['commande', 'magasin'])->get(); // Relations si elles existent
        $ligneReceptions = LigneReception::all();
        $commandes = Commande::all(); // Pour le formulaire
        $ligneCommandes = LigneCommande::all();
        $magasins = Magasin::all(); // Pour le formulaire
        return view('pages.approvisionnement.receptions', compact('receptions', 'commandes', 'magasins'));
    }

    // Enregistrer une nouvelle réception
    public function generer(Request $request)
    {
        $validated = $request->validate([
            'umReception' => 'required|string|max:50',
            'dateReception' => 'required|date',
            'RefNum' => 'required|string|max:50',
            'BonReception' => 'nullable|string|max:50',
            'idCmd' => 'required|exists:commandes,id', // Vérifie que la commande existe
            'idE' => 'required|exists:magasins,id', // Vérifie que le magasin existe
        ]);

        Reception::create($validated);

        return redirect()->route('receptions.reception')->with('success', 'Réception enregistrée avec succès.');
    }
}