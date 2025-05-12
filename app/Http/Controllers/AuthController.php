<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'showProfile', 'updateProfile', 'showChangePasswordForm', 'changePassword']);
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/tableaudebord');
        }

        // Récupérer la liste des bases de données
        $databases = DB::select('SHOW DATABASES');
        $databaseNames = [];
        foreach ($databases as $database) {
            $databaseNames[] = $database->Database;
        }

        return view('auth.login', ['databases' => $databaseNames]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'mail' => 'required|email',
            'password' => 'required',
            'database' => 'required',
        ]);

        try {
            // Mettre à jour la configuration de la base de données
            config(['database.connections.mysql.database' => $request->database]);
            DB::purge('mysql');
            DB::reconnect('mysql');

            // Vérifier si la connexion à la base de données est réussie
            DB::connection()->getPdo();

            $credentials = $request->only('mail', 'password');

            if (Auth::attempt($credentials)) {
                // Stocker la base de données sélectionnée en session
                session(['selected_database' => $request->database]);
                $request->session()->regenerate();
                return redirect()->intended('/tableaudebord');
            }

            return back()->withErrors([
                'mail' => "Les informations d'identification ne correspondent pas.",
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'database' => "Impossible de se connecter à la base de données sélectionnée.",
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login'); // Redirigez vers la page de connexion
    }

    public function showProfile()
    {
        $utilisateur = Auth::user();
        return view('auth.profile', compact('utilisateur'));
    }

    public function updateProfile(Request $request)
    {
        $utilisateur = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mail' => 'required|string|email|max:255|unique:utilisateurs,mail,' . $utilisateur->idU . ',idU',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $utilisateur->update([
            'nom' => $validated['name'],
            'mail' => $validated['mail'],
            'photo' => $request->hasFile('photo') ? file_get_contents($request->file('photo')->getRealPath()) : $utilisateur->photo
        ]);

        return redirect()->route('profile')->with('success', 'Profil mis à jour avec succès');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $utilisateur = Auth::user();

        if (!Hash::check($request->current_password, $utilisateur->password)) {
            return back()->withErrors([
                'current_password' => 'Le mot de passe actuel est incorrect.',
            ]);
        }

        $utilisateur->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('profile')->with('success', 'Mot de passe modifié avec succès');
    }
}