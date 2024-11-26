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
Route::post('/ajouterFournisseur', [FournisseurController::class, 'ajouterFournisseur']);
Route::delete('suppFournisseur/{id}', [FournisseurController::class, 'deleteFournisseur']);
Route::put('modifFournisseur/{id}', [FournisseurController::class, 'updateFournisseur']);

// clientcontroller
Route::get('/client', [ClientController::class, 'client']);
Route::post('/ajouterClient', [ClientController::class, 'ajouterClient']);
Route::delete('suppClient/{id}', [ClientController::class, 'deleteClient']);
Route::put('modifClient/{id}', [ClientController::class, 'updateClient']);


Route::get('/produits', [ProduitsController::class, 'index'])->name('produits');
Route::get('/categories', [CategoriesController::class, 'index'])->name('categories');
Route::delete('/categories/{id}', [CategoriesController::class, 'destroy'])->name('categories.destroy');
// Route pour traiter l'ajout d'une nouvelle catégorie (la méthode store)
Route::post('/categories/store', [CategoriesController::class, 'store'])->name('categories.store');
Route::get('/clients', [FournisseurController::class, 'clients'])->name('clients');


// ApprovisionnementController
Route::get('Approvisionnement/commandeAchat', [ApprovisionnementController::class, 'commandeAchat'])->name('commandeAchat');


Route::get('/tableaudebord', [TableauController::class, 'tableaudebord']);
Route::get('/parametres/utilisateurs', [ParamController::class, 'utilisateurs']);
Route::get('/caisses', [Controller::class, 'index'])->name('caisses.index');
Route::post('/caisses', [Controller::class, 'store'])->name('caisses.store');
Route::put('/caisses/{id}', [Controller::class, 'update'])->name('caisses.update');
Route::delete('/caisses/{id}', [Controller::class, 'destroy'])->name('caisses.destroy');