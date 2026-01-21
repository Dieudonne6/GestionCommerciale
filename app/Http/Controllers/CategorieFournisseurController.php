<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Contracts\Validation\Validator;
use App\Models\CategorieFournisseur;

class CategorieFournisseurController extends Controller
{
    public function index()
    {
        // Récupérer toutes les catégories de la table 'categoriesFournisseur'
    $categoriesF = CategorieFournisseur::all();  // Cette méthode récupère toutes les lignes de la table

    // Retourner la vue avec les catégories récupérées
    return view('pages.Fournisseur&Achat.categoriefournisseur', compact('categoriesF')); 
    }

    // Méthode pour enregistrer une nouvelle catégorie
    public function store(Request $request)
    {

        
        // Validation des données
        $validator = \Validator::make($request->all(), [
            'codeCatFour' => 'required|string|min:5|max:255|unique:categorie_fournisseurs,codeCatFour', // Ajout de min:5 pour imposer au moins 5 caractères
            'libelle' => 'required|string|min:5|max:255|unique:categorie_fournisseurs,libelle',// Ajout de min:5 pour imposer au moins 5 caractères
        ], [
            'codeCatFour.required' => 'Le code de la catégorie est obligatoire.',
            'codeCatFour.min' => 'Le code de catégorie doit contenir au moins 5 caractères.',
            'codeCatFour.max' => 'Le code de catégorie ne peut pas dépasser 255 caractères.',
            'codeCatFour.unique' => 'Ce code de catégorie existe déjà.',
            'libelle.required' => 'Le nom de la catégorie est obligatoire.',
            'libelle.min' => 'Le nom de la catégorie doit contenir au moins 5 caractères.',
            'libelle.max' => 'Le nom de la catégorie ne peut pas dépasser 255 caractères.',
            'libelle.unique' => 'Ce nom de catégorie existe déjà.',
        ]);

            // Vérifier si la validation a échoué
        if ($validator->fails()) {
            return redirect()->route('categoriesF')  // Recharger la page
                ->withErrors($validator)  // Envoyer les erreurs en session
                ->withInput()  // Garder les anciennes valeurs
                ->with('showAddCategoryModal', true); // Ajouter un flag pour ouvrir le modal après le rechargement
        }
    
        // Créer une nouvelle catégorie
        $categoryF = new CategorieFournisseur();
        $categoryF->codeCatFour = $request->input('codeCatFour');  // Utiliser 'codeC' comme nom de la colonne dans la base
        $categoryF->libelle = $request->input('libelle');  // Utiliser 'NomC' comme nom de la colonne dans la base
        $categoryF->save();  // Enregistrer la catégorie dans la base de données
        //dd($categoryF);
        // Rediriger avec un message de succès
        return redirect()->route('categoriesF')->with('success', 'Catégorie ajoutée avec succès !');
    }

    public function destroy($id)
    {
        // Récupérer la catégorie à supprimer
        $categoryF = CategorieFournisseur::findOrFail($id);
    
        // Supprimer la catégorie
        $categoryF->delete();
    
        // Retourner avec succès
        return redirect()->route('categoriesF')->with('success', 'Catégorie supprimée avec succès !');
    }

    public function update(Request $request, $id)
    {
        $categoryF = CategorieFournisseur::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'codeCatFour' => 'required|string|min:5|max:255|unique:categorie_fournisseurs,codeCatFour,'.$id.',idCatFour',
            'libelle' => 'required|string|min:5|max:255|unique:categorie_fournisseurs,libelle,'.$id.',idCatFour',
        ], [
            'codeCatFour.required' => 'Le code de la catégorie est obligatoire.',
            'codeCatFour.min' => 'Le code de la catégorie doit contenir au moins 5 caractères.',
            'codeCatFour.max' => 'Le code de la catégorie ne peut pas dépasser 255 caractères.',
            'codeCatFour.unique' => 'Ce code de catégorie existe déjà.',
            'libelle.required' => 'Le nom de la catégorie est obligatoire.',
            'libelle.min' => 'Le nom de la catégorie doit contenir au moins 5 caractères.',
            'libelle.max' => 'Le nom de la catégorie ne peut pas dépasser 255 caractères.',
            'libelle.unique' => 'Ce nom de catégorie existe déjà.',
        ]);

if ($validator->fails()) {
    session()->flash('showModifyCategoryModal', $id);
    return redirect()->route('categoriesF')->withErrors($validator);
}

        // Mise à jour des données de la catégorie
        $categoryF->libelle = $request->libelle;
        $categoryF->codeCatFour = $request->codeCatFour;


        $categoryF->save();

        return redirect()->route('categoriesF')->with('success', 'Catégorie mise à jour avec succès!');
    }


public function edit($id)
{
    // Récupérer la catégorie par ID
    $categoryF = CategorieFournisseur::findOrFail($id);

    // Retourner la vue avec les données de la catégorie
    return view('categories', compact('categoriesF'));
}
}