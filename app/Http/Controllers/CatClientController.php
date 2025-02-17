<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategorieClient;
use Illuminate\Validation\ValidationException;

class CatClientController extends Controller
{
    public function categorieclient()
    {
        $categorie_clients = CategorieClient::all();
        return view('pages.GestClient.categorieclient', compact('categorie_clients')); 
    }

    // Ajout d'une nouvelle catégorie client avec validation
    public function ajouterCategoryclient(Request $request)
    {
        $request->validate([
            'codeCatCl' => 'required|string|max:255|unique:categorie_clients,codeCatCl',
            'libelle'   => 'required|string|max:255',
        ]);

        try {
            CategorieClient::create($request->only(['codeCatCl', 'libelle']));
            return back()->with('status', 'La catégorie client a été créée avec succès.');
        } catch (\Exception $e) {
            return back()->with('erreur', 'Une erreur est survenue lors de l\'ajout.');
        }
    }

    // Suppression d'une catégorie client
    public function deletecategorieclient($idCatCl)
    {
        try {
            $categorieClient = CategorieClient::findOrFail($idCatCl);
            $categorieClient->delete();
            return back()->with('status', 'La catégorie client a été supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('erreur', 'Catégorie client introuvable ou erreur de suppression.');
        }
    }

    // Modification d'une catégorie client avec validation
    public function updatecategorieclient(Request $request, $idCatCl)
    {
        $request->validate([
            'codeCatCl' => 'required|string|max:255|unique:categorie_clients,codeCatCl,' . $idCatCl . ',idCatCl',
            'libelle'   => 'required|string|max:255',
        ]);

        try {
            $modifCategorieClient = CategorieClient::findOrFail($idCatCl);
            $modifCategorieClient->update($request->only(['codeCatCl', 'libelle']));
            return back()->with('status', 'La catégorie client a été modifiée avec succès.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('erreur', 'Erreur lors de la modification.');
        }
    }
}