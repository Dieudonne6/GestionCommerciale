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
    $allEntreprises = Entreprise::get();
    // $allEntrepris = Entreprise::get();
    return view('pages.parametres.entreprise', compact('allEntreprises'));
}



public function ajouterEntreprise( EntrepriseRequest $request ) {

    $request->validated();

    // dd($request->all());
    $entrepriseExiste = Entreprise::where('IFU', $request->input('IFU'))
    ->orWhere('nom', $request->input('nom'))
    ->exists();

    if ($entrepriseExiste) {
        return back()->with(['erreur' => 'Cette Entreprise existe déjà.']);
    }

    $idParent = empty($request->input('idParent')) ? null : intval($request->input('idParent'));

    try {
        $Entreprise = new Entreprise();
        $Entreprise->IFU = $request->input('IFU');
        $Entreprise->nom = $request->input('nom');
        $Entreprise->telephone = $request->input('telephone');
        $Entreprise->mail = $request->input('mail');
        $Entreprise->adresse = $request->input('adresse');
        $Entreprise->RCCM = $request->input('RCCM');
        $Entreprise->regime = $request->input('regime');
        $Entreprise->idParent = $idParent;
        if ($request->hasFile('logo')) {
            $imageContent = file_get_contents($request->file('logo')->getRealPath());
            $Entreprise->logo = $imageContent;
        } 
        // $imageContent = file_get_contents($request->file('logo')->getRealPath());
        // $Entreprise->logo = $imageContent;
        $Entreprise->save();

        return back()->with("status", "L'entreprise a été creer avec succes");

    } catch (\Exception $e) {
        // Stockez l'ID du modal d'ajout en cas d'erreur
        return redirect()->back()
            ->withErrors($e->getMessage())
            ->with('errorModalId', 'addBoardModal'); // ID du modal d'ajout
    }

   
}



public function supprimerEntreprise ($idE) {
    $entreprise = Entreprise::where('idE', $idE)->first();
    $entreprise->delete();
    return back()->with("status", "L'Entreprise a été supprimer avec succes");
}



public function modifEntreprise ( EntrepriseRequest $request, $idE ) {

    $request->validated();

    $idParent = empty($request->input('idParent')) ? null : intval($request->input('idParent'));

    try {

        $modifEntreprise = Entreprise::where('idE', $idE)->first();
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
        $modifEntreprise->update();  
        return back()->with("status", "L'Entreprise a été modifier avec succes");

    } catch (\Exception $e) {
        // Stockez l'ID du modal dans la session en cas d'erreur
        return redirect()->back()
        ->withErrors($e->getMessage())
        ->with('errorModalId', 'ModifyBoardModal' . $idCatPro); // ID dynamique du modal de modification
    }



}


// export entreprise 



public function entrepriseExport()
{
    // Récupérer toutes les tables de la base de données
    $tables = DB::select('SHOW TABLES');
    $tableNames = [];

    foreach ($tables as $table) {
        // Récupérer le nom de chaque table
        $tableNames[] = $table->Tables_in_gestioncommerciale; // Remplacez "your_database_name" par le nom de votre base de données
    }
    
    // Récupérer toutes les bases de données
    $baseDeDonnes = DB::select('SHOW DATABASE');
    $dbNames = [];

    foreach ($baseDeDonnes as $baseDeDonne) {
        // Récupérer le nom de chaque table
        $dbNames[] = $baseDeDonne->gestioncommerciale; // Remplacez "your_database_name" par le nom de votre base de données
    }

    // Passer la liste des tables à la vue
    return view('pages.exporterTable.exporttable', ['tables' => $tableNames] , ['dbNames' => $dbNames]);
}


public function Export(Request $request)
{
    // Définir les tables autorisées
    // $allowedTables = ['entreprise', 'clients', 'commandes'];

    // Valider que la table demandée est dans la liste autorisée
    $table = $request->input('table');
    // if (!in_array($table, $allowedTables)) {
    //     return redirect()->back()->with('error', 'Table non autorisée.');
    // }

    $fileName = $table . '_' . date('Y-m-d_H-i-s') . '.xlsx';

    // Lancer l'export avec Laravel Excel
    // return Excel::download(new GenericExport($table), $table . '.csv', \Maatwebsite\Excel\Excel::CSV);

    return Excel::download(new GenericExport($table), $fileName);
}


// public function entrepriseExport() {
//     return Excel::download(new EntreprisesExport, 'entreprises.xlsx');
// }


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