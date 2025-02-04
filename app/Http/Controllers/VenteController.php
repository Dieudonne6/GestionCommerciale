<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Produit;
use App\Models\Vente;
use App\Models\LigneVente;

class VenteController extends Controller
{
    public function vente() {
        $allClients = Client::get();
        $allproduits = Produit::get();
        $allVente = Vente::with('client', 'lignesVente')->get(); // Ajout de 'lignesVente'
        return view('pages.Facturation.vente', compact('allClients','allproduits','allVente'));
    }



}
