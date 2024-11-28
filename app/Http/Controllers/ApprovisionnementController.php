<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fournisseur;
use App\Models\Produit;
use App\Models\LigneCommande;

class ApprovisionnementController extends Controller
{
    //

    public function commandeAchat() {
        return view('pages.Approvisionnement.commandeAchat');
    }
    public function reception() {
        return view('pages.Approvisionnement.reception');
    }
    public function ajoutercommande() {
        $allfournisseurs = Fournisseur::get();
        $allproduits = Produit::get();

        return view('pages.Approvisionnement.ajoutercommande', compact('allfournisseurs','allproduits'));
    }
    public function ajouterLignCmd(Request $request)
{
    // Validation des données
    $request->validate([
        'idP' => 'required|exists:produits,idP',
        'quantity' => 'required|numeric|min:1',
        'price' => 'required|numeric|min:0',
    ]);

    // Traitement des données
    $line = [
        'idP' => $request->idP,
        'qteCmd' => $request->quantity,
        'prix' => $request->price
        // 'tva' => $request->price * 0.2, 
        // 'ttc' => $request->price * 1.2
    ];

    // Retour en JSON
    return response()->json([
        'success' => true,
        'line' => $line
    ]);
}

    
}
