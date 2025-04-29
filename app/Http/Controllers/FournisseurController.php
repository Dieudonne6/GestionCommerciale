<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;

use App\Models\Fournisseur;
use App\Models\CategorieFournisseur;

class FournisseurController extends Controller
{
    public function fournisseur()
    {
        $allfournisseurs = Fournisseur::with('categorieFournisseur')->get();
        $categoriesF = CategorieFournisseur::all();
        return view('pages.Fournisseur&Achat.fournisseur', compact('allfournisseurs', 'categoriesF'));
    }

    // Création d'un fournisseur
    public function ajouterFournisseur(Request $request)
    {
        // Validation des données
        $validator = \Validator::make($request->all(), [
            'IFU' => 'required|string|min:5|max:50|unique:fournisseurs,IFU',
            'nom' => 'required|string|min:3|max:255',
            'adresse' => 'required|string|min:5|max:255',
            'telephone' => 'required|string|min:8|max:15|unique:fournisseurs,telephone',
            'mail' => 'required|email|max:255|unique:fournisseurs,mail',
            'idCatFour' => 'required|exists:categorie_fournisseurs,idCatFour',
        ], [
            'IFU.required' => 'L\'IFU est obligatoire.',
            'IFU.min' => 'L\'IFU doit contenir au moins 5 caractères.',
            'IFU.unique' => 'Cet IFU existe déjà.',
            'nom.required' => 'Le nom est obligatoire.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.unique' => 'Ce numéro de téléphone existe déjà.',
            'mail.required' => 'L\'adresse e-mail est obligatoire.',
            'mail.email' => 'L\'adresse e-mail n\'est pas valide.',
            'mail.unique' => 'Cette adresse e-mail existe déjà.',
            'idCatFour.required' => 'La catégorie du fournisseur est obligatoire.',
            'idCatFour.exists' => 'La catégorie sélectionnée est invalide.',
        ]);

        // Vérifier si la validation a échoué
        if ($validator->fails()) {
            return redirect()->route('fournisseur')
                ->withErrors($validator)
                ->withInput()
                ->with('showAddFournisseurModal', true);
        }

        // Création du fournisseur
        
            $Fournisseur = new Fournisseur();
            $Fournisseur->IFU = $request->input('IFU');
            $Fournisseur->nom = $request->input('nom');
            $Fournisseur->adresse = $request->input('adresse');
            $Fournisseur->telephone = $request->input('telephone');
            $Fournisseur->mail = $request->input('mail');
            $Fournisseur->idCatFour = $request->input('idCatFour');
            $Fournisseur->save();

            return redirect()->route('fournisseur')->with('success', 'Fournisseur ajouté avec succès !');

    }

    // Suppression d'un fournisseur
    public function deleteFournisseur($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->delete();

        return redirect()->route('fournisseur')->with('success', 'Fournisseur supprimé avec succès !');
    }

    // Mise à jour d'un fournisseur
    public function updateFournisseur(Request $request, $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);

        // Validation des données
        $validator = \Validator::make($request->all(), [
            'IFU' => 'required|string|min:5|max:50|unique:fournisseurs,IFU,' . $id . ',idF',
            'nom' => 'required|string|min:3|max:255',
            'adresse' => 'required|string|min:5|max:255',
            'telephone' => 'required|string|min:8|max:15|unique:fournisseurs,telephone,' . $id . ',idF',
            'mail' => 'required|email|max:255|unique:fournisseurs,mail,' . $id . ',idF',
            'idCatFour' => 'required|exists:categorie_fournisseurs,idCatFour',
        ], [
            'IFU.required' => 'L\'IFU est obligatoire.',
            'IFU.min' => 'L\'IFU doit contenir au moins 5 caractères.',
            'IFU.unique' => 'Cet IFU existe déjà.',
            'nom.required' => 'Le nom est obligatoire.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.unique' => 'Ce numéro de téléphone existe déjà.',
            'mail.required' => 'L\'adresse e-mail est obligatoire.',
            'mail.email' => 'L\'adresse e-mail n\'est pas valide.',
            'mail.unique' => 'Cette adresse e-mail existe déjà.',
            'idCatFour.required' => 'La catégorie du fournisseur est obligatoire.',
            'idCatFour.exists' => 'La catégorie sélectionnée est invalide.',
        ]);

        // Vérifier si la validation a échoué
        if ($validator->fails()) {
            session()->flash('showModifyFournisseurModal', $id);
            return redirect()->route('fournisseur')->withErrors($validator);
        }

        // Mise à jour du fournisseur
    
            $fournisseur->IFU = $request->input('IFU');
            $fournisseur->nom = $request->input('nom');
            $fournisseur->adresse = $request->input('adresse');
            $fournisseur->telephone = $request->input('telephone');
            $fournisseur->mail = $request->input('mail');
            $fournisseur->idCatFour = $request->input('idCatFour');
            $fournisseur->update();

            return redirect()->route('fournisseur')->with('success', 'Fournisseur mis à jour avec succès !');

    }
}