<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\CategorieClient;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        $categories = CategorieClient::all();
        return view('pages.GestClient.client', compact('clients', 'categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IFU' => 'required|digits:13|unique:clients,IFU',
            'nom'       => 'required|string|max:255',
            'adresse'   => 'required|string|max:255',
            'telephone' => 'required|string|max:50',
            'mail'      => 'required|email|max:255|unique:clients,mail',
            'idCatCl'   => 'required|exists:categorie_clients,idCatCl'
        ], [
            'IFU.required'       => 'L\'IFU est obligatoire.',
            'IFU.digits'         => 'L\'IFU doit comporter exactement 13 chiffres.',
            'IFU.unique'         => 'Cet IFU existe déjà.',
            'nom.required'       => 'Le nom est obligatoire.',
            'adresse.required'   => 'L\'adresse est obligatoire.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'mail.required'      => 'L\'email est obligatoire.',
            'mail.email'         => 'L\'email doit être valide.',
            'mail.unique'        => 'Cet email est déjà utilisé.',
            'idCatCl.required'   => 'La catégorie client est obligatoire.',
            'idCatCl.exists'     => 'La catégorie client sélectionnée est invalide.'
        ]);

        if ($validator->fails()) {
            return redirect()->route('clients.index')
                ->withErrors($validator)
                ->withInput()
                ->with('showAddClientModal', true);
        }

        try {
            Client::create($request->only(['IFU', 'nom', 'adresse', 'telephone', 'mail', 'idCatCl']));
            return redirect()->route('clients.index')
                ->with('status', 'Le client a été créé avec succès');
        } catch (\Exception $e) {
            return redirect()->route('clients.index')
                ->with('erreur', 'Une erreur est survenue lors de la création du client');
        }
    }

    public function update(Request $request, $idC)
    {
        $client = Client::findOrFail($idC);

        $validator = Validator::make($request->all(), [
            'IFU'       => 'required|digits:13|unique:clients,IFU,'.$client->idC.',idC',
            'nom'       => 'required|string|max:255',
            'adresse'   => 'required|string|max:255',
            'telephone' => 'required|string|max:50',
            'mail'      => 'required|email|max:255|unique:clients,mail,'.$client->idC.',idC',
            'idCatCl'   => 'required|exists:categorie_clients,idCatCl'
        ], [
            'IFU.required'       => 'L\'IFU est obligatoire.',
            'IFU.digits'         => 'L\'IFU doit comporter exactement 13 chiffres.',
            'IFU.unique'         => 'Cet IFU existe déjà.',
            'nom.required'       => 'Le nom est obligatoire.',
            'adresse.required'   => 'L\'adresse est obligatoire.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'mail.required'      => 'L\'email est obligatoire.',
            'mail.email'         => 'L\'email doit être valide.',
            'mail.unique'        => 'Cet email est déjà utilisé.',
            'idCatCl.required'   => 'La catégorie client est obligatoire.',
            'idCatCl.exists'     => 'La catégorie client sélectionnée est invalide.'
        ]);

        if ($validator->fails()) {
            // Flag pour ouvrir le modal de modification avec l'ID concerné
            session()->flash('showModifyClientModal', $idC);
            return redirect()->route('clients.index')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $client->update($request->only(['IFU', 'nom', 'adresse', 'telephone', 'mail', 'idCatCl']));
            return redirect()->route('clients.index')
                ->with('status', 'Le client a été modifié avec succès');
        } catch (\Exception $e) {
            return redirect()->route('clients.index')
                ->with('erreur', 'Une erreur est survenue lors de la modification du client');
        }
    }

    public function destroy($idC)
    {
        try {
            $client = Client::findOrFail($idC);
            $client->delete();
            return redirect()->route('clients.index')
                ->with('status', 'Le client a été supprimé avec succès');
        } catch (\Exception $e) {
            return redirect()->route('clients.index')
                ->with('erreur', 'Une erreur est survenue lors de la suppression du client');
        }
    }
}