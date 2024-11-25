<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ProduitsController;
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
Route::get('/fournisseur', [FournisseurController::class, 'fournisseur'])->name('fournisseur');
Route::get('/produits', [ProduitsController::class, 'index'])->name('produits');
Route::get('/clients', [FournisseurController::class, 'clients'])->name('clients');


// ApprovisionnementController
Route::get('Approvisionnement/commandeAchat', [ApprovisionnementController::class, 'commandeAchat'])->name('commandeAchat');


Route::get('/tableaudebord', [TableauController::class, 'tableaudebord']);
Route::get('/parametres/utilisateurs', [ParamController::class, 'utilisateurs']);
Route::get('/caisses', [Controller::class, 'caisses']);
