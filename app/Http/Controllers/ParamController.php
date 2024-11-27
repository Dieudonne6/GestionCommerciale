<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Entreprise;
use App\Models\Role;

class ParamController extends Controller
{
    // Affiche la liste des utilisateurs
    public function utilisateurs()
    {
        $users = User::all();
        return view('pages.parametres.utilisateurs', compact('users'));
    }

    // Ajoute un nouvel utilisateur
    public function enregistre(Request $request)
    {
        $request->validate([
            'login' => 'required|string|unique:users',
            'nomU' => 'required|string|max:255',
            'adresseU' => 'required|string',
            'telephone' => 'required|string|max:15',
            'password' => 'required|min:6',
            'roleID' => 'required|integer',
        ]);

        User::create([
            'login' => $request->login,
            'nomU' => $request->nomU,
            'adresseU' => $request->adresseU,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password),
            'roleID' => $request->roleID,
        ]);

        return redirect()->back()->with('success', 'Utilisateur ajouté avec succès.');
    }

    // Met à jour un utilisateur existant
    public function modifie(Request $request, $idU)
    {
        $user = User::findOrFail($idU);

        $request->validate([
            'login' => 'required|string|unique:users,login,' . $idU . ',idU',
            'nomU' => 'required|string|max:255',
            'adresseU' => 'required|string',
            'telephone' => 'required|string|max:15',
            'roleID' => 'required|integer',
        ]);

        $user->update([
            'login' => $request->login,
            'nomU' => $request->nomU,
            'adresseU' => $request->adresseU,
            'telephone' => $request->telephone,
            'roleID' => $request->roleID,
        ]);

        return redirect()->back()->with('success', 'Utilisateur mis à jour avec succès.');
    }

    // Supprime un utilisateur
    public function supprime($idU)
    {
        $user = User::findOrFail($idU);
        $user->delete();

        return redirect()->back()->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function entreprise()
    {
        $entreprise = Entreprise::first();
        return view('pages.parametres.entreprise', compact('entreprise'));
    }

    public function storeEntreprise(Request $request)
    {
        $request->validate([
            'nomEntreprise' => 'required|string|max:255',
            'adresseEntreprise' => 'nullable|string|max:255',
            'emailEntreprise' => 'nullable|email',
            'telephone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'IFU' => 'nullable|string|max:50',
            'Description' => 'nullable|string',
            'site_web' => 'nullable|url',
        ]);

        $data = $request->all();

        // Gestion du logo
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $logoPath;
        }

        $entreprise = Entreprise::first();
        if ($entreprise) {
            $entreprise->update($data);
        } else {
            Entreprise::create($data);
        }

        return redirect()->back()->with('success', 'Informations de l\'entreprise mises à jour avec succès.');
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
             'libelleRole' => 'required|string|max:255|unique:roles,libelleRole',
         ]);
 
         Role::create([
             'libelleRole' => $request->libelleRole,
         ]);
 
         return redirect()->back()->with('success', 'Rôle ajouté avec succès.');
     }
 
     // Modifie un rôle
     public function updateRole(Request $request, $id)
     {
         $request->validate([
             'libelleRole' => 'required|string|max:255|unique:roles,libelleRole,' . $id,
         ]);
 
         $role = Role::findOrFail($id);
         $role->update([
             'libelleRole' => $request->libelleRole,
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