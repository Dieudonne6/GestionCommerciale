<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\CategorieClient;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        $categories = CategorieClient::all();
        return view('pages.definition.client', compact('clients', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'IFU'       => 'required|numeric|unique:clients,IFU',
            'nom'       => 'required|string|max:255',
            'adresse'   => 'required|string|max:255',
            'telephone' => 'required|string|max:50',
            'mail'      => 'required|email|max:255|unique:clients,mail',
            'idCatCl'   => 'required|exists:categorie_clients,idCatCl'
        ]);

        $client = new Client();
        $client->IFU       = $validated['IFU'];
        $client->nom       = $validated['nom'];
        $client->adresse   = $validated['adresse'];
        $client->telephone = $validated['telephone'];
        $client->mail      = $validated['mail'];
        $client->idCatCl   = $validated['idCatCl'];
        $client->save();

        return back()->with('status', 'Le client a été créé avec succès');
    }

    public function update(Request $request, $idC)
    {
        $client = Client::findOrFail($idC);

        $validated = $request->validate([
            'IFU'       => 'required|numeric|unique:clients,IFU,'.$client->idC.',idC',
            'nom'       => 'required|string|max:255',
            'adresse'   => 'required|string|max:255',
            'telephone' => 'required|string|max:50',
            'mail'      => 'required|email|max:255|unique:clients,mail,'.$client->idC.',idC',
            'idCatCl'   => 'required|exists:categorie_clients,idCatCl'
        ]);

        $client->IFU       = $validated['IFU'];
        $client->nom       = $validated['nom'];
        $client->adresse   = $validated['adresse'];
        $client->telephone = $validated['telephone'];
        $client->mail      = $validated['mail'];
        $client->idCatCl   = $validated['idCatCl'];
        $client->save();

        return back()->with('status', 'Le client a été modifié avec succès');
    }

    public function destroy($idC)
    {
        $client = Client::findOrFail($idC);
        $client->delete();

        return back()->with('status', 'Le client a été supprimé avec succès');
    }
}