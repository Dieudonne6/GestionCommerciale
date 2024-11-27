<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
}