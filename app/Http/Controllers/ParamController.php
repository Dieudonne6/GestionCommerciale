<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use App\Models\Entreprise;
use App\Models\Role;

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
       $request->validate([
           'nom'       => 'required|string|max:255',
           'adresse'   => 'required|string',
           'telephone' => 'required|string|max:15',
           'mail'      => 'required|email|unique:utilisateurs,mail',
           'idRole'    => 'required|integer',
           // Vous pouvez ajouter une validation pour idE si nécessaire :
           'idE'    => 'nullable|integer',
       ]);

       Utilisateur::create([
           'nom'       => $request->nom,
           'adresse'   => $request->adresse,
           'telephone' => $request->telephone,
           'mail'      => $request->mail,
           'idRole'    => $request->idRole,
           'idE'       => $request->idE, // Assurez-vous que ce champ est présent dans votre formulaire ou définissez-le en dur si nécessaire.
       ]);

       return redirect()->back()->with('success', 'Utilisateur ajouté avec succès.');
   }

   // Met à jour un utilisateur existant
   public function modifie(Request $request, $idU)
   {
       $utilisateur = Utilisateur::findOrFail($idU);

       $request->validate([
           'nom'       => 'required|string|max:255' . $idU . ',idU',
           'adresse'   => 'required|string',
           'telephone' => 'required|string|max:15',
           'mail'      => 'required|email|unique:utilisateurs,mail,' . $idU . ',idU',
           'idRole'    => 'required|integer',
           'idE'    => 'nullable|integer',
       ]);

       $utilisateur->update([
           'nom'       => $request->nom,
           'adresse'   => $request->adresse,
           'telephone' => $request->telephone,
           'mail'      => $request->mail,
           'idRole'    => $request->idRole,
           'idE'       => $request->idE,
       ]);

       return redirect()->back()->with('success', 'Utilisateur mis à jour avec succès.');
   }

   // Supprime un utilisateur
   public function supprime($idU)
   {
       $utilisateur = Utilisateur::findOrFail($idU);
       $utilisateur->delete();

       return redirect()->back()->with('success', 'Utilisateur supprimé avec succès.');
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