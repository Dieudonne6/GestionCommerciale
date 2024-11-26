<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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
            'categoryName' => 'required|string|max:255',  // Validation pour le nom de la catégorie
            'categoryImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation pour l'image
        ]);
    
        // Gérer l'upload de l'image
        if ($request->hasFile('categoryImage')) {
            // Stocker l'image dans le dossier 'public/categories'
            $imagePath = $request->file('categoryImage')->store('categories', 'public');
        }
    
        // Créer une nouvelle catégorie
        $category = new Categorie();
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

        // Supprimer l'image associée à la catégorie si elle existe
        if (File::exists(public_path('storage/' . $category->imgC))) {
            File::delete(public_path('storage/' . $category->imgC));
        }

        // Supprimer la catégorie
        $category->delete();

        // Retourner à la liste avec un message de succès
        return redirect()->route('categories')->with('success', 'Catégorie supprimée avec succès !');
    }
}
