<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Categorie;

class ProduitsController extends Controller
{
    public function index()
    {
        // Charge les produits avec leur catégorie associée
        $produits = Produit::with('categorie')->get();
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
        $produit->stockDown = $request->input('stockDown');
        $produit->categorieP = $request->input('categorie');
        $produit->PrixVente = $request->input('prix');
        $produit->save();
        return redirect()->route('produits')->with('success', 'Produit ajouté avec succès');
    }

    public function update(Request $request, $idP)
    {
        $produit = Produit::findOrFail($idP);

        $request->validate([
            'nom' => 'required',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'quantite' => 'required',
            'categorie' => 'required',
            'prix' => 'required',
        ]);

        $produit->NomP = $request->nom;
        $produit->descP = $request->description;
        /* $produit->imgP = $request->input('image');  */
        $produit->qteP = $request->quantite;
        $produit->categorieP = $request->categorie;
        $produit->stockDown = $request->stockDown;
        $produit->PrixVente = $request->prix;

        // Vérifier si une nouvelle image est envoyée
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($produit->imgP && Storage::disk('public')->exists('produits/' . basename($produit->imgP))) {
                Storage::disk('public')->delete('produits/' . basename($produit->imgP));
            }

            // Sauvegarder la nouvelle image
            $imagePath = $request->file('image')->store('produits', 'public');
            $produit->imgP = $imagePath;
        }

        $produit->save();

        return redirect()->route('produits')->with('success', 'Produit modifié avec succès');
    }

    public function destroy($idP)
    {
        $produit = Produit::findOrFail($idP);
    
        // Vérifier si l'image existe et la supprimer
        if ($produit->imgP && Storage::disk('public')->exists($produit->imgP)) {
            Storage::disk('public')->delete($produit->imgP);
        }
    
        // Supprimer le produit de la base de données
        $produit->delete();
    
        return redirect()->route('produits')->with('success', 'Produit supprimé avec succès');
    }
    

}
