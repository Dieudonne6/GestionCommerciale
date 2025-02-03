<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use App\Models\Categorie;

class CategoriesController extends Controller
{
    public function index()
    {
        // Récupérer toutes les catégories de la table 'categories'
    $categories = Categorie::all();  // Cette méthode récupère toutes les lignes de la table

    // Retourner la vue avec les catégories récupérées
    return view('pages.categories', compact('categories')); 
    }

    // Méthode pour enregistrer une nouvelle catégorie
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'categoryCode' => 'required|string|max:255', // Validation pour le code de la catégorie
            'categoryName' => 'required|string|max:255',  // Validation pour le nom de la catégorie
            'categoryImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation pour l'image
        ]);
       // dd($request->all());
        // Gérer l'upload de l'image
        if ($request->hasFile('categoryImage')) {
            // Stocker l'image dans le dossier 'public/categories'
            $imagePath = $request->file('categoryImage')->store('categories', 'public');
        }
    
        // Créer une nouvelle catégorie
        $category = new Categorie();
        $category->codeC = $request->input('categoryCode');  // Utiliser 'codeC' comme nom de la colonne dans la base
        $category->NomC = $request->input('categoryName');  // Utiliser 'NomC' comme nom de la colonne dans la base
        $category->imgC = $imagePath;  // Utiliser 'imgC' pour l'image
        $category->save();  // Enregistrer la catégorie dans la base de données
    
        // Rediriger avec un message de succès
        return redirect()->route('categories')->with('success', 'Catégorie ajoutée avec succès !');
    }

    public function destroy($id)
    {
        // Récupérer la catégorie à supprimer
        $category = Categorie::findOrFail($id);
    
        if ($category->imgC && Storage::disk('public')->exists('categories/' . basename($category->imgC))) {
            Storage::disk('public')->delete('categories/' . basename($category->imgC));
        }
    
        // Supprimer la catégorie
        $category->delete();
    
        // Retourner avec succès
        return redirect()->route('categories')->with('success', 'Catégorie supprimée avec succès !');
    }

    public function update(Request $request, $id)
    {
        $category = Categorie::findOrFail($id);

        // Validation des données du formulaire
        $request->validate([
            'categoryCode' => 'required|string|max:255', // Validation pour le code de la catégorie
            'categoryName' => 'required|string|max:255',
            'categoryImage' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        // Mise à jour des données de la catégorie
        $category->NomC = $request->categoryName;
        $category->codeC = $request->categoryCode;

        // Vérifier si une nouvelle image est envoyée
        if ($request->hasFile('categoryImage')) {

            // Supprimer l'ancienne image si elle existe
            if ($category->imgC && Storage::disk('public')->exists('categories/' . basename($category->imgC))) {
                Storage::disk('public')->delete('categories/' . basename($category->imgC));
            }

            // Sauvegarder la nouvelle image
            $imagePath = $request->file('categoryImage')->store('categories', 'public');
            $category->imgC = $imagePath;
        }

        $category->save();

        return redirect()->route('categories')->with('success', 'Catégorie mise à jour avec succès!');
    }


public function edit($id)
{
    // Récupérer la catégorie par ID
    $category = Categorie::findOrFail($id);

    // Retourner la vue avec les données de la catégorie
    return view('categories', compact('category'));
}
}
