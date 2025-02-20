<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategorieClient;
use Illuminate\Support\Facades\Validator;

class CatClientController extends Controller
{
    // Affiche toutes les catégories clients
    public function categorieclient()
    {
        $categorie_clients = CategorieClient::all();
        return view('pages.GestClient.categorieclient', compact('categorie_clients')); 
    }

    // Ajout d'une nouvelle catégorie client avec validation
    public function ajouterCategoryclient(Request $request)
    {
        // Création du validateur pour la validation manuelle
        $validator = Validator::make($request->all(), [
            'codeCatCl' => 'required|string|max:255|unique:categorie_clients,codeCatCl',
            'libelle'   => 'required|string|max:255',
        ], [
            'codeCatCl.required' => 'Le code de la catégorie client est obligatoire.',
            'codeCatCl.unique'   => 'Ce code de catégorie client existe déjà.',
            'libelle.required'   => 'Le libellé de la catégorie client est obligatoire.',
        ]);

        // Si la validation échoue, on redirige avec les erreurs et on indique d'ouvrir le modal
        if ($validator->fails()) {
            return redirect()->route('categorieclient')
                ->withErrors($validator)
                ->withInput()
                ->with('showAddCategoryModal', true);
        }

        try {
            // Création de la nouvelle catégorie client
            CategorieClient::create($request->only(['codeCatCl', 'libelle']));
            return redirect()->route('categorieclient')
                ->with('status', 'La catégorie client a été créée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('categorieclient')
                ->with('erreur', 'Une erreur est survenue lors de l\'ajout.');
        }
    }

    // Suppression d'une catégorie client
    public function deletecategorieclient($idCatCl)
    {
        try {
            $categorieClient = CategorieClient::findOrFail($idCatCl);
            $categorieClient->delete();
            return redirect()->route('categorieclient')
                ->with('status', 'La catégorie client a été supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('categorieclient')
                ->with('erreur', 'Catégorie client introuvable ou erreur de suppression.');
        }
    }

    // Modification d'une catégorie client avec validation
    public function updatecategorieclient(Request $request, $idCatCl)
    {
        $categorieClient = CategorieClient::findOrFail($idCatCl);

        // Création du validateur pour la mise à jour
        $validator = Validator::make($request->all(), [
            'codeCatCl' => 'required|string|max:255|unique:categorie_clients,codeCatCl,' . $idCatCl . ',idCatCl',
            'libelle'   => 'required|string|max:255',
        ], [
            'codeCatCl.required' => 'Le code de la catégorie client est obligatoire.',
            'codeCatCl.unique'   => 'Ce code de catégorie client existe déjà.',
            'libelle.required'   => 'Le libellé de la catégorie client est obligatoire.',
        ]);

        // En cas d'erreur, on redirige en conservant l'ID du modal à ouvrir
        if ($validator->fails()) {
            session()->flash('showModifyCategoryModal', $idCatCl);
            return redirect()->route('categorieclient')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Mise à jour de la catégorie client
            $categorieClient->update($request->only(['codeCatCl', 'libelle']));
            return redirect()->route('categorieclient')
                ->with('status', 'La catégorie client a été modifiée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('categorieclient')
                ->with('erreur', 'Erreur lors de la modification.');
        }
    }
}