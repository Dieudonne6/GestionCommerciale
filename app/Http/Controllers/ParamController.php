<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Utilisateur;
use App\Models\Entreprise;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\EntrepriseRequest;
<<<<<<< HEAD
use Illuminate\Support\Facades\Config;



=======
>>>>>>> 8c1e66499864c2625b4b7a4a088c08339b0b81ad
use App\Exports\EntreprisesExport;
use App\Exports\GenericExport;
use Maatwebsite\Excel\Facades\Excel;

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
            'password'  => 'required|string|min:8|confirmed',
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
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('showAddUserModal', true);
        }

        try {
            $data = $validator->validated();
            $data['password'] = Hash::make($data['password']);
            Utilisateur::create($data);
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

    // Affiche la liste des entreprises
    public function entreprise()
    {
        $allEntreprises = Entreprise::get();
        return view('pages.parametres.entreprise', compact('allEntreprises'));
    }

    // Ajoute une nouvelle entreprise
    public function ajouterEntreprise(EntrepriseRequest $request)
    {
        $request->validated();

        // Vérifier si l'entreprise existe déjà
        $entrepriseExiste = Entreprise::where('IFU', $request->input('IFU'))
            ->orWhere('nom', $request->input('nom'))
            ->exists();

        if ($entrepriseExiste) {
            return back()->with(['erreur' => 'Cette entreprise existe déjà.']);
        }

        $idParent = empty($request->input('idParent')) ? null : intval($request->input('idParent'));

        try {
            $entreprise = new Entreprise();
            $entreprise->IFU = $request->input('IFU');
            $entreprise->nom = $request->input('nom');
            $entreprise->telephone = $request->input('telephone');
            $entreprise->mail = $request->input('mail');
            $entreprise->adresse = $request->input('adresse');
            $entreprise->RCCM = $request->input('RCCM');
            $entreprise->regime = $request->input('regime');
            $entreprise->idParent = $idParent;
            if ($request->hasFile('logo')) {
                $imageContent = file_get_contents($request->file('logo')->getRealPath());
                $entreprise->logo = $imageContent;
            }
            $entreprise->save();

            return back()->with("status", "L'entreprise a été créée avec succès.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->with('errorModalId', 'addBoardModal'); // ID du modal d'ajout
        }
    }

    // Supprime une entreprise
    public function supprimerEntreprise($idE)
    {
        try {
            $entreprise = Entreprise::where('idE', $idE)->firstOrFail();
            $entreprise->delete();
            return back()->with("status", "L'entreprise a été supprimée avec succès.");
        } catch (\Exception $e) {
            return redirect()->back()->with('erreur', 'Une erreur est survenue lors de la suppression de l\'entreprise.');
        }
    }

    // Modifie une entreprise existante
    public function modifEntreprise(EntrepriseRequest $request, $idE)
    {
        $request->validated();

        $idParent = empty($request->input('idParent')) ? null : intval($request->input('idParent'));

        try {
            $modifEntreprise = Entreprise::where('idE', $idE)->firstOrFail();
            $modifEntreprise->nom = $request->input('nom');
            $modifEntreprise->IFU = $request->input('IFU');
            $modifEntreprise->telephone = $request->input('telephone');
            $modifEntreprise->mail = $request->input('mail');
            $modifEntreprise->adresse = $request->input('adresse');
            $modifEntreprise->RCCM = $request->input('RCCM');
            $modifEntreprise->regime = $request->input('regime');
            $modifEntreprise->idParent = $idParent;
            if ($request->hasFile('logo')) {
                $imageContent = file_get_contents($request->file('logo')->getRealPath());
                $modifEntreprise->logo = $imageContent;
            }
            $modifEntreprise->save();
            return back()->with("status", "L'entreprise a été modifiée avec succès.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->with('errorModalId', 'ModifyBoardModal' . $idE); // Utilisation de $idE pour identifier le modal
        }
    }

    // Affiche l'export des entreprises (liste des tables et des bases)
    public function entrepriseExport()
    {
        // Récupérer toutes les tables de la base de données
        $tables = DB::select('SHOW TABLES');
        $tableNames = [];

        foreach ($tables as $table) {
            // Assurez-vous ici que la colonne correspond bien au nom de votre base de données
            $tableNames[] = $table->Tables_in_gestioncommerciale;
        }

        // Récupérer toutes les bases de données
        $baseDeDonnes = DB::select('SHOW DATABASES');
        $dbNames = [];

        foreach ($baseDeDonnes as $base) {
            // En fonction du SGBD, le nom de la colonne peut être "Database"
            $dbNames[] = $base->Database;
        }

        // Passer la liste des tables et des bases à la vue
        return view('pages.exporterTable.exporttable', ['tables' => $tableNames, 'dbNames' => $dbNames]);
    }

    // Export d'une table donnée
    // public function Export(Request $request)
    // {
    //     $table = $request->input('table');
    //     $fileName = $table . '_' . date('Y-m-d_H-i-s') . '.xlsx';

    //     return Excel::download(new GenericExport($table), $fileName);
    // }

    // Méthode d'exportation alternative (définie ici en commentaire)
    // public function entrepriseExport() {
    //     return Excel::download(new EntreprisesExport, 'entreprises.xlsx');
    // }
}