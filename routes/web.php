<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProduitsController;
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
Route::post('/ajouterClient', [ClientController::class, 'ajouterClient']);
Route::delete('suppClient/{id}', [ClientController::class, 'deleteClient']);
Route::put('modifClient/{id}', [ClientController::class, 'updateClient']);


Route::get('/produits', [ProduitsController::class, 'index'])->name('produits');


// ApprovisionnementController
Route::get('Approvisionnement/commandeAchat', [ApprovisionnementController::class, 'commandeAchat'])->name('commandeAchat');


