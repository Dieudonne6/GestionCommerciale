<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorie;

class CategoriesController extends Controller
{
    public function index()
    {
        return view('pages.categories');
    }

    // Méthode pour enregistrer une nouvelle catégorie
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'categoryName' => 'required|string|max:255',
            'categoryImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Taille max 2MB
        ]);

        // Gérer l'upload de l'image
        if ($request->hasFile('categoryImage')) {
            $imagePath = $request->file('categoryImage')->store('categories', 'public'); // Stockage dans storage/app/public/categories
        }

        // Créer la catégorie
        $category = new Categorie();
        $category->name = $request->input('categoryName');
        $category->image = $imagePath; // L'image est enregistrée avec son chemin relatif
        $category->save();

        // Rediriger avec un message de succès
        return redirect()->route('categories.index')->with('success', 'Catégorie ajoutée avec succès !');
    }
}
