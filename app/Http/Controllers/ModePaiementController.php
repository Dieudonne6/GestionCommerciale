<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ModePaiement;
use Illuminate\Support\Facades\Validator;

class ModePaiementController extends Controller
{
    // Affiche la liste des modes de paiement
    public function index()
    {
        $modePaiements = ModePaiement::orderBy('idModPaie', 'desc')->get();
        return view('pages.parametres.modepaiement', compact('modePaiements'));
    }

    // Ajoute un nouveau mode de paiement
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255|unique:mode_paiements,libelle',
        ], [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.string' => 'Le libellé doit être une chaîne de caractères.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 255 caractères.',
            'libelle.unique' => 'Ce mode de paiement existe déjà.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('showAddModal', true);
        }

        try {
            ModePaiement::create($validator->validated());
            return redirect()->back()->with('status', 'Mode de paiement ajouté avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('erreur', 'Une erreur est survenue lors de l\'ajout du mode de paiement.');
        }
    }

    // Met à jour un mode de paiement existant
    public function update(Request $request, $idModPaie)
    {
        $modePaiement = ModePaiement::findOrFail($idModPaie);

        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255|unique:mode_paiements,libelle,' . $idModPaie . ',idModPaie',
        ], [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.string' => 'Le libellé doit être une chaîne de caractères.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 255 caractères.',
            'libelle.unique' => 'Ce mode de paiement existe déjà.',
        ]);

        if ($validator->fails()) {
            session()->flash('showModifyModal', $idModPaie);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $modePaiement->update($validator->validated());
            return redirect()->back()->with('status', 'Mode de paiement mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('erreur', 'Une erreur est survenue lors de la modification du mode de paiement.');
        }
    }

    // Supprime un mode de paiement
    public function destroy($idModPaie)
    {
        try {
            $modePaiement = ModePaiement::findOrFail($idModPaie);
            
            // Vérifier si le mode de paiement est utilisé dans des ventes
            if ($modePaiement->vente()->count() > 0) {
                return redirect()->back()->with('erreur', 'Ce mode de paiement ne peut pas être supprimé car il est utilisé dans des ventes.');
            }
            
            $modePaiement->delete();
            return redirect()->back()->with('status', 'Mode de paiement supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('erreur', 'Une erreur est survenue lors de la suppression du mode de paiement.');
        }
    }
}
