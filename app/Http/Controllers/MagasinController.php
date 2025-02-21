<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;

use App\Models\Magasin;
use App\Models\Entreprise;
use App\Models\Produit;
use App\Models\Stocke;
use App\Models\CategorieProduit;
use App\Models\FamilleProduit;

class MagasinController extends Controller
{
    // Afficher la liste des magasins
    public function index()
    {
        $magasins = Magasin::with('entreprise')->get();
        $entreprises = Entreprise::all();
        $produits = Produit::all(); // Pour le modal d'ajout de produit
        $allCategorieProduits = CategorieProduit::get();
        $allFamilleProduits = FamilleProduit::get();
        return view('pages.ProduitStock.magasins', compact('magasins', 'produits', 'entreprises', 'allCategorieProduits', 'allFamilleProduits'));
    }

    public function ajouterMagasin(Request $request)
    {
        // Validation des données
        $validator = \Validator::make($request->all(), [
            'libelle' => 'required|string|max:250',
            'codeMagasin' => 'required|string|min:3|max:255|unique:magasins,codeMagasin',
            'Adresse' => 'required|string|min:5|max:255',
            'idE' => 'required',
        ], [
            'libelle.required' => 'L\'IFU est obligatoire.',
            'codeMagasin.unique' => 'Cet Magasin existe déjà.',
            'codeMagasin.required' => 'Un code est requis.',
            'Adresse.required' => 'L\'adresse est obligatoire.',
            'idE.required' => 'Ce champ est obligatoire.',
        ]);

        // Vérifier si la validation a échoué
        if ($validator->fails()) {
            return redirect()->route('magasins')
                ->withErrors($validator)
                ->withInput()
                ->with('showAddMagasinModal', true);
        }

        // Création du fournisseur
        
            $magasins = new Magasin();
            $magasins->libelle = $request->input('libelle');
            $magasins->codeMagasin = $request->input('codeMagasin');
            $magasins->Adresse = $request->input('Adresse');
            $magasins->idE = $request->input('idE');
            $magasins->save();

            return redirect()->route('magasins')->with('success', 'Magasin ajouté avec succès !');
    } 
    // Afficher les détails d'un magasin (produits associés)

    // Ajouter un produit au magasin
    public function addProduct(Request $request, $idMag)
    {
        // Vérifier si le produit existe déjà
        $produit = Produit::where('libelle', $request->libelle)->first();

        $validator = \Validator::make($request->all(), [
            'libelle' => 'required|string|max:255',
            'qteStocke' => 'required|integer|min:1',
            'idCatPro' => 'required',
            'idFamPro' => 'required',
        ], [
            'idCatPro.required' => 'Ce champ est obligatoire.',
            'idFamPro.required' => 'Ce champ est obligatoire.',
        ]);

        // Vérifier si la validation a échoué
        if ($validator->fails()) {
            session()->flash('showModifyMagasinModal', $idMag);
            return redirect()->route('magasins')->withErrors($validator);
        }

        if (!$produit) {
            // Création du produit s'il n'existe pas
            $produit = Produit::create([
                'libelle' => $request->libelle,
                'prix' => $request->prix,
                'desc' => $request->desc,
                'image' => $request->image,
                'stockAlert' => $request->stockAlert,
                'stockMinimum' => $request->stockMinimum,
                'idCatPro' => $request->idCatPro,
                'idFamPro' => $request->idFamPro,
            ]);
        }

        // Vérifier si ce produit est déjà en stock dans ce magasin
        $stock = Stocke::where('idPro', $produit->idPro)
                       ->where('idMag', $idMag)
                       ->first();

        if ($stock) {
            // Mise à jour de la quantité existante
            $stock->qteStocke += $request->qteStocke;
            $stock->save();
        } else {
            // Ajouter un nouvel enregistrement de stock
            Stocke::create([
                'idPro' => $produit->idPro,
                'idMag' => $idMag,
                'qteStocke' => $request->qteStocke,
                'CUMP' => 0, // Mettre à jour CUMP si nécessaire
            ]);
        }

        return redirect()->back()->with('success', 'Produit ajouté au stock avec succès !');
    } 

    // Supprimer un magasin
    public function destroy($id)
    {
        $magasin = Magasin::findOrFail($id);
        $magasin->delete();
        return redirect()->route('pages.ProduitStock.magasins')->with('success', 'Magasin supprimé avec succès.');
    }

    // Mettre à jour un magasin
/*     public function update(Request $request, $id)
    {
        $magasin = Magasin::findOrFail($id);

        $request->validate([
            'libelle' => 'required|string|min:5|max:255',
            'codeMagasin' => 'required|string|min:5|max:255|unique:magasins,codeMagasin,' . $id . ',idMag',
            'Adresse' => 'required|string|min:5|max:255',
        ]);

        $magasin->update($request->all());

        return redirect()->route('pages.ProduitStock.magasins')->with('success', 'Magasin mis à jour avec succès.');
    } */
}

