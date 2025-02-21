<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use App\Models\Entreprise;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;

class ParamController extends Controller
{
   // Affiche la liste des utilisateurs
   public function utilisateurs()
   {
       $utilisateurs = Utilisateur::all();
       $roles = Role::all();
       $entreprises = Entreprise::all();
       return view('pages.parametres.utilisateurs', compact('utilisateurs', 'roles', 'entreprises'));
   }

   // Ajoute un nouvel utilisateur
public function enregistre(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nom'       => 'required|string|max:255',
        'adresse'   => 'required|string|max:255',
        'telephone' => 'required|string|max:15',
        'mail'      => 'required|email|max:255|unique:utilisateurs,mail',
        'idRole'    => 'required|integer',
        'idE'       => 'nullable|integer',
    ], [
        'nom.required'       => 'Le nom est obligatoire.',
        'nom.string'         => 'Le nom doit être une chaîne de caractères.',
        'nom.max'            => 'Le nom ne peut pas dépasser 255 caractères.',
        'adresse.required'   => 'L\'adresse est obligatoire.',
        'adresse.string'     => 'L\'adresse doit être une chaîne de caractères.',
        'adresse.max'        => 'L\'adresse ne peut pas dépasser 255 caractères.',
        'telephone.required' => 'Le téléphone est obligatoire.',
        'telephone.string'   => 'Le téléphone doit être une chaîne de caractères.',
        'telephone.max'      => 'Le téléphone ne peut pas dépasser 15 caractères.',
        'mail.required'      => 'L\'email est obligatoire.',
        'mail.email'         => 'L\'email doit être valide.',
        'mail.max'           => 'L\'email ne peut pas dépasser 255 caractères.',
        'mail.unique'        => 'Cet email est déjà utilisé.',
        'idRole.required'    => 'Le rôle est obligatoire.',
        'idRole.integer'     => 'Le rôle doit être un entier.',
        'idE.integer'        => 'L\'ID de l\'entité doit être un entier.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('showAddUserModal', true);
    }

    try {
        Utilisateur::create($validator->validated());
        return redirect()->back()->with('success', 'Utilisateur ajouté avec succès.');
    } catch (\Exception $e) {
        return redirect()->back()->with('erreur', 'Une erreur est survenue lors de la création de l\'utilisateur.');
    }
}

// Met à jour un utilisateur existant
public function modifie(Request $request, $idU)
{
    $utilisateur = Utilisateur::findOrFail($idU);

    $validator = Validator::make($request->all(), [
        'nom'       => 'required|string|max:255',
        'adresse'   => 'required|string|max:255',
        'telephone' => 'required|string|max:15',
        'mail'      => 'required|email|max:255|unique:utilisateurs,mail,' . $idU . ',idU',
        'idRole'    => 'required|integer',
        'idE'       => 'nullable|integer',
    ], [
        'nom.required'       => 'Le nom est obligatoire.',
        'nom.string'         => 'Le nom doit être une chaîne de caractères.',
        'nom.max'            => 'Le nom ne peut pas dépasser 255 caractères.',
        'adresse.required'   => 'L\'adresse est obligatoire.',
        'adresse.string'     => 'L\'adresse doit être une chaîne de caractères.',
        'adresse.max'        => 'L\'adresse ne peut pas dépasser 255 caractères.',
        'telephone.required' => 'Le téléphone est obligatoire.',
        'telephone.string'   => 'Le téléphone doit être une chaîne de caractères.',
        'telephone.max'      => 'Le téléphone ne peut pas dépasser 15 caractères.',
        'mail.required'      => 'L\'email est obligatoire.',
        'mail.email'         => 'L\'email doit être valide.',
        'mail.max'           => 'L\'email ne peut pas dépasser 255 caractères.',
        'mail.unique'        => 'Cet email est déjà utilisé.',
        'idRole.required'    => 'Le rôle est obligatoire.',
        'idRole.integer'     => 'Le rôle doit être un entier.',
        'idE.integer'        => 'L\'ID de l\'entité doit être un entier.',
    ]);

    if ($validator->fails()) {
        session()->flash('showModifyUserModal', $idU);
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    try {
        $utilisateur->update($validator->validated());
        return redirect()->back()->with('success', 'Utilisateur mis à jour avec succès.');
    } catch (\Exception $e) {
        return redirect()->back()->with('erreur', 'Une erreur est survenue lors de la modification de l\'utilisateur.');
    }
}

// Supprime un utilisateur
public function supprime($idU)
{
    try {
        $utilisateur = Utilisateur::findOrFail($idU);
        $utilisateur->delete();
        return redirect()->back()->with('success', 'Utilisateur supprimé avec succès.');
    } catch (\Exception $e) {
        return redirect()->back()->with('erreur', 'Une erreur est survenue lors de la suppression de l\'utilisateur.');
    }
}


   public function entreprise()
{
    $entreprises = Entreprise::all();
    return view('pages.parametres.entreprise', compact('entreprises'));
}

public function storeEntreprise(Request $request)
{
    $validated = $request->validate([
        'nom'       => 'required|string|max:255',
        'IFU'       => 'nullable|string|max:50',
        'adresse'   => 'nullable|string|max:255',
        'telephone' => 'nullable|string|max:20',
        'mail'      => 'nullable|email|max:255',
        'logo'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'RCCM'      => 'nullable|string|max:100',
        'regime'    => 'nullable|string|max:100',
        'idParent'  => 'nullable|exists:entreprises,idE'
    ]);

    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('logos', 'public');
        $validated['logo'] = $logoPath;
    }

    Entreprise::create($validated);

    return redirect()->back()->with('success', 'Entreprise ajoutée avec succès.');
}

public function updateEntreprise(Request $request, $id)
{
    $entreprise = Entreprise::findOrFail($id);

    $validated = $request->validate([
        'nom'       => 'required|string|max:255',
        'IFU'       => "required|string|max:50|unique:entreprises,IFU,{$id},idE",
        'adresse'   => 'nullable|string|max:255',
        'telephone' => 'nullable|string|max:20',
        'mail'      => 'nullable|email|max:255',
        'logo'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'RCCM'      => 'nullable|string|max:100',
        'regime'    => 'nullable|string|max:100',
        'idParent'  => 'nullable|exists:entreprises,idE'
    ]);

    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('logos', 'public');
        $validated['logo'] = $logoPath;
    }

    $entreprise->update($validated);

    return redirect()->back()->with('success', 'Entreprise mise à jour avec succès.');
}

public function destroyEntreprise($id)
{
    $entreprise = Entreprise::findOrFail($id);
    $entreprise->delete();

    return redirect()->back()->with('success', 'Entreprise supprimée avec succès.');
}

     // Affiche les rôles
     public function role()
     {
         $roles = Role::all();
         return view('pages.parametres.roles', compact('roles'));
     }
 
     // Ajoute un rôle
public function storeRole(Request $request)
{
    $request->validate([
        'libelle' => 'required|string|max:255|unique:roles,libelle',
    ]);

    Role::create([
        'libelle' => $request->libelle,
    ]);

    return redirect()->back()->with('success', 'Rôle ajouté avec succès.');
}

// Modifie un rôle
public function updateRole(Request $request, $id)
{
    $request->validate([
        // Ici, on ignore l'enregistrement courant grâce à l'id et on précise la colonne de la clé primaire (idRole)
        'libelle' => 'required|string|max:255|unique:roles,libelle,' . $id . ',idRole',
    ]);

    $role = Role::findOrFail($id);
    $role->update([
        'libelle' => $request->libelle,
    ]);

    return redirect()->back()->with('success', 'Rôle modifié avec succès.');
}

// Supprime un rôle
public function deleteRole($id)
{
    $role = Role::findOrFail($id);
    $role->delete();

    return redirect()->back()->with('success', 'Rôle supprimé avec succès.');
}
}