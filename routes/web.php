<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProduitsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\TableauController;
use App\Http\Controllers\ParamController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApprovisionnementController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
//fournisseurcontroller
Route::get('/fournisseur', [FournisseurController::class, 'fournisseur']);
Route::post('/ajouterFournisseur', [FournisseurController::class, 'ajouterFournisseur'])->name('fournisseur.ajouter');
Route::delete('suppFournisseur/{id}', [FournisseurController::class, 'deleteFournisseur']);
Route::put('modifFournisseur/{id}', [FournisseurController::class, 'updateFournisseur'])->name('fournisseur.update');

// clientcontroller
Route::get('/client', [ClientController::class, 'client']);
Route::post('/ajouterClient', [ClientController::class, 'ajouterClient']);
Route::delete('suppClient/{id}', [ClientController::class, 'deleteClient']);
Route::put('modifClient/{id}', [ClientController::class, 'updateClient']);


Route::get('/produits', [ProduitsController::class, 'index'])->name('produits');
Route::post('/produits/store', [ProduitsController::class, 'store'])->name('produits.store');
Route::put('/produits/{idP}', [ProduitsController::class, 'update'])->name('produits.update');
Route::delete('/produits/{idP}', [ProduitsController::class, 'destroy'])->name('produits.destroy');
Route::get('/categories', [CategoriesController::class, 'index'])->name('categories');
Route::delete('/categories/{id}', [CategoriesController::class, 'destroy'])->name('categories.destroy');
Route::put('/categories/{id}', [CategoriesController::class, 'update'])->name('categories.update');
Route::get('/categories/edit/{id}', [CategoriesController::class, 'edit'])->name('categories.edit');
// Route pour traiter l'ajout d'une nouvelle catégorie (la méthode store)
Route::post('/categories/store', [CategoriesController::class, 'store'])->name('categories.store');
Route::get('/clients', [FournisseurController::class, 'clients'])->name('clients');


// ApprovisionnementController
Route::get('commandeAchat', [ApprovisionnementController::class, 'commandeAchat'])->name('commandeAchat');
Route::get('ajoutercommande', [ApprovisionnementController::class, 'ajoutercommande'])->name('ajoutercommande');

Route::post('/ajouterlignCmd', [ApprovisionnementController::class, 'ajouterLignCmd']);

Route::get('/tableaudebord', [TableauController::class, 'tableaudebord']);
Route::get('/caisses', [Controller::class, 'index'])->name('caisses.index');
Route::post('/caisses', [Controller::class, 'store'])->name('caisses.store');
Route::put('/caisses/{id}', [Controller::class, 'update'])->name('caisses.update');
Route::delete('/caisses/{id}', [Controller::class, 'destroy'])->name('caisses.destroy');


Route::get('/parametres/utilisateurs', [ParamController::class, 'utilisateurs'])->name('users.utilisateurs');
Route::post('/parametres/utilisateurs', [ParamController::class, 'enregistre'])->name('users.enregistre');
Route::post('/parametres/utilisateurs/{idU}/modifier', [ParamController::class, 'modifie'])->name('users.modifie');
Route::delete('/parametres/utilisateurs/{idU}/supprimer', [ParamController::class, 'supprime'])->name('users.supprime');

Route::get('/parametres/entreprise', [ParamController::class, 'entreprise'])->name('entreprise.entreprise');
Route::post('/parametres/entreprise', [ParamController::class, 'storeEntreprise'])->name('entreprise.storeEntreprise');

Route::get('/parametres/roles', [ParamController::class, 'role'])->name('role');
Route::post('/parametres/roles/store', [ParamController::class, 'storeRole'])->name('storeRole');
Route::post('/parametres/roles/update/{id}', [ParamController::class, 'updateRole'])->name('updateRole');
Route::get('/parametres/roles/delete/{id}', [ParamController::class, 'deleteRole'])->name('deleteRole');
Route::get('/reception', [ApprovisionnementController::class, 'reception'])->name('reception');