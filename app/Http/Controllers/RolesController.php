<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;


class RolesController extends Controller
{
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