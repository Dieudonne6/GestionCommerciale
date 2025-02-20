<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\LigneVente;

class RapportController extends Controller
{
    public function Affichage(Request $request)
{
    // Récupération des ventes avec leurs relations 'client', 'vendeur' et 'lignesVente.produit'
    $query2 = LigneVente::all();
    $query = Vente::with(['client', 'vendeur', 'lignesVente.produit']);

    // Vérification des filtres soumis par l'utilisateur
    if ($request->filled('numV')) {
        $query->where('numV', 'LIKE', "%{$request->numV}%");
    }

    if ($request->filled('dateOperation')) {
        $query->whereDate('dateOperation', $request->dateOperation);
    }

    // Exécution de la requête
    $ventes = $query->get();


    return view('pages.rapportventes', compact('ventes','query2'));
}

}

