<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Categorie;

class ProduitsController extends Controller
{
    public function index()
    {
        $produits = Produit::all();
        $categories = Categorie::all();
        return view('pages.produits', compact('produits', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'description' => 'required',
            'imageproduit' => 'required|image|mimes:jpeg,png,jpg,gif|max:5048',
            'quantite' => 'required',
            'categorie' => 'required',
            'prix' => 'required',
        ]);
        if ($request->hasFile('imageproduit')) {
            // Stocker l'image dans le dossier 'public/categories'
            $imagePath = $request->file('imageproduit')->store('produits', 'public');
        }
        $produit = new Produit();
        $produit->NomP = $request->input('nom');
        $produit->descP = $request->input('description');
        $produit->imgP = $imagePath;
        $produit->qteP = $request->input('quantite');
        $produit->categorieP = $request->input('categorie');
        $produit->PrixVente = $request->input('prix');
        $produit->save();
        return redirect()->route('produits')->with('success', 'Produit ajouté avec succès');
    }

    public function update(Request $request, $idP)
    {
        $produit = Produit::find($idP);
        $produit->NomP = $request->input('nom');
        $produit->descP = $request->input('description');
        $produit->imgP = $request->input('image');
        $produit->qteP = $request->input('quantite');
        $produit->categorieP = $request->input('categorie');
        $produit->PrixVente = $request->input('prix');
        $produit->save();
        return redirect()->route('produits')->with('success', 'Produit modifié avec succès');
    }

    public function destroy($idP)
    {
        $produit = Produit::find($idP);
        $produit->delete();
        return redirect()->route('produits')->with('success', 'Produit supprimé avec succès');
    }
}
