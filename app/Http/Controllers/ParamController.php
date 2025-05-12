<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Utilisateur;
use App\Models\Entreprise;
use App\Models\Role;
use App\Http\Requests\EntrepriseRequest;
use Illuminate\Support\Facades\Config;



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


// export table 



public function Exporttable()
{
    // Récupérer les bases de données disponibles
    $databases = DB::select('SHOW DATABASES');
    $databaseNames = [];

    foreach ($databases as $database) {
        // Récupérer le nom de chaque base de données
        $databaseNames[] = $database->Database;
    }

    // Passer la liste des bases de données à la vue
    return view('pages.exporterTable.exporttable', ['databases' => $databaseNames]);
}

public function getTables($databaseName)
{
    try {
        // Créer une connexion dynamique à la base de données
        Config::set('database.connections.dynamic', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $databaseName,
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        // Utiliser cette connexion dynamique
        $tables = DB::connection('dynamic')->select('SHOW TABLES');
        $tableNames = [];

        foreach ($tables as $table) {
            $tableNames[] = $table->{"Tables_in_" . $databaseName}; // Dynamique selon la base de données
        }

        return response()->json($tableNames);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Impossible de se connecter à la base de données: ' . $e->getMessage()], 500);
    }
}


public function Export(Request $request)
{
    $databaseName = $request->input('database');
    $tableName = $request->input('table');

    try {
        // Configurer dynamiquement la connexion à la base de données choisie
        Config::set('database.connections.dynamic', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $databaseName, // Base de données choisie par l'utilisateur
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        // Utiliser la connexion dynamique
        $data = DB::connection('dynamic')->table($tableName)->get();

        // Utiliser Maatwebsite Excel pour exporter les données
        return Excel::download(new GenericExport($tableName), $tableName . '.xlsx');
        
    } catch (\Exception $e) {
        return response()->json(['error' => 'Erreur de connexion à la base de données : ' . $e->getMessage()], 500);
    }
}


public function Exportss(Request $request)
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