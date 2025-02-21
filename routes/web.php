<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategorieFournisseurController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProduitsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\TableauController;
use App\Http\Controllers\ParamController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApprovisionnementController;
use App\Http\Controllers\ExerciceController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\CatClientController;
use App\Http\Controllers\FamilleProduitController;
use App\Http\Controllers\CategorieProduitController;

use App\Http\Controllers\MagasinController;

use App\Http\Controllers\ProduitController;


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
Route::get('/fournisseur', [FournisseurController::class, 'fournisseur'])->name('fournisseur');
Route::post('/ajouterFournisseur', [FournisseurController::class, 'ajouterFournisseur'])->name('fournisseurs.ajouterFournisseur');
Route::delete('suppFournisseur/{id}', [FournisseurController::class, 'deleteFournisseur']);
Route::put('modifFournisseur/{id}', [FournisseurController::class, 'updateFournisseur'])->name('fournisseur.update');

// clientcontroller
Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
Route::put('/clients/{idC}', [ClientController::class, 'update'])->name('clients.update');
Route::delete('/clients/{idC}', [ClientController::class, 'destroy'])->name('clients.destroy');

// categorieclientcontroller
Route::get('/categorieclient', [CatClientController::class, 'categorieclient'])->name('categorieclient');
Route::post('/categorieclient/ajouter', [CatClientController::class, 'ajouterCategoryclient'])->name('categorieclient.ajouter');
Route::delete('/categorieclient/supprimer/{idCatCl}', [CatClientController::class, 'deletecategorieclient'])->name('categorieclient.supprimer');
Route::put('/categorieclient/modifier/{idCatCl}', [CatClientController::class, 'updatecategorieclient'])->name('categorieclient.modifier');

// Exercicecontroller
Route::get('/exercice', [ExerciceController::class, 'exercice']);
Route::post('/ajouterExercice', [ExerciceController::class, 'ajouterExercice'])->name('ajouterExercice');
// Route::delete('suppClient/{id}', [ExerciceController::class, 'deleteClient']);
Route::put('activerExercice/{id}', [ExerciceController::class, 'activerExercice'])->name('activerExercice');


Route::get('/produits', [ProduitsController::class, 'index'])->name('produits');
Route::post('/produits/store', [ProduitsController::class, 'store'])->name('produits.store');
Route::put('/produits/{idP}', [ProduitsController::class, 'update'])->name('produits.update');
Route::delete('/produits/{idP}', [ProduitsController::class, 'destroy'])->name('produits.destroy');
Route::get('/categories', [CategoriesController::class, 'index'])->name('categories');
Route::delete('/categories/{id}', [CategoriesController::class, 'destroy'])->name('categories.destroy');
Route::put('/categories/{id}', [CategoriesController::class, 'update'])->name('categories.update');
Route::get('/categories/edit/{id}', [CategoriesController::class, 'edit'])->name('categories.edit');

//Fournisseur
Route::get('/categoriesFournisseur', [CategorieFournisseurController::class, 'index'])->name('categoriesF');
Route::post('/categorieFournisseur/store', [CategorieFournisseurController::class, 'store'])->name('categoriesF.store');
Route::delete('/categoriesFournisseur/{id}', [CategorieFournisseurController::class, 'destroy'])->name('categoriesF.destroy');
Route::put('/categoriesFournisseur/{id}', [CategorieFournisseurController::class, 'update'])->name('categoriesF.update');
Route::get('/categoriesFournisseur/edit/{id}', [CategorieFournisseurController::class, 'edit'])->name('categoriesF.edit');

// Magasin
Route::get('/magasins', [MagasinController::class, 'index'])->name('magasins');
Route::post('/ajouterMagasin', [MagasinController::class, 'ajouterMagasin'])->name('magasins.ajouterMagasin');
Route::delete('suppMagasin/{id}', [MagasinController::class, 'destroy'])->name('magasins.destroy');
Route::post('addProduct/{idMag}', [MagasinController::class, 'addProduct'])->name('magasins.addProduct');
//Route::put('modifMagasin/{id}', [MagasinController::class, 'updateMagasin'])->name('magasins.updateMagasin');

// Route pour traiter l'ajout d'une nouvelle catégorie (la méthode store)
Route::post('/categories/store', [CategoriesController::class, 'store'])->name('categories.store');
// Route pour traiter l'ajout d'une nouvelle catégorie (la méthode store)
Route::post('/categories/store', [CategoriesController::class, 'store'])->name('categories.store');
Route::get('/client', [FournisseurController::class, 'client'])->name('client');


// ApprovisionnementController
Route::get('commandeAchat', [ApprovisionnementController::class, 'commandeAchat'])->name('commandeAchat');
Route::get('ajoutercommande', [ApprovisionnementController::class, 'ajoutercommande'])->name('ajoutercommande');

Route::post('/ligne-commande', [ApprovisionnementController::class, 'ajouterLigneCommande'])->name('ajouterLigneCommande');

// Route::post('ajouterlignCmd', [ApprovisionnementController::class, 'ajouterLignCmd'])->name('ajouterLignCmd');

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


// Entreprise
Route::get('/entreprise', [ParamController::class, 'entreprise'])->name('entreprise');
Route::post('/ajouterEntreprise', [ParamController::class, 'ajouterEntreprise'])->name('ajouterEntreprise');
Route::put('modifierEntreprise/{idE}', [ParamController::class, 'modifEntreprise'])->name('modifEntreprise');
Route::delete('suppEntreprise/{idE}', [ParamController::class, 'supprimerEntreprise'])->name('supprimerEntreprise');


Route::get('/roles', [ParamController::class, 'role'])->name('role');
Route::post('/roles/store', [ParamController::class, 'storeRole'])->name('storeRole');
Route::post('/roles/update/{id}', [ParamController::class, 'updateRole'])->name('updateRole');
Route::get('/roles/delete/{id}', [ParamController::class, 'deleteRole'])->name('deleteRole');

// Reception
Route::get('/receptions', [Controller::class, 'indexReception'])->name('receptions.index');
Route::post('/receptions', [Controller::class, 'storeReception'])->name('receptions.store');
Route::put('/receptions/{idReception}', [Controller::class, 'updateReception'])->name('receptions.update');
Route::delete('/receptions/{idReception}', [Controller::class, 'destroyReception'])->name('receptions.destroy');

Route::post('/ajouterCmd', [Controller::class, 'storeCmd'])->name('ajouterCmd.store');
// Route::put('/ajouterCmd/{idCmd}', [Controller::class, 'updateCmd'])->name('ajouterCmd.update');
Route::delete('/commande/{idCmd}', [Controller::class, 'destroyCommande'])->name('commande.destroy');
Route::put('modifCmd/{idCmd}', [Controller::class, 'updateCmd']);
Route::get('modifCmd/{idCmd}', [Controller::class, 'updateCmd']);
Route::delete('/deleteLigneCommande/{id}', [Controller::class, 'deleteLigneCommande']);
// -------------------------------
Route::post('/ajouterVente', [Controller::class, 'storeVente'])->name('ajouterVente.store');
// Route::put('/ajouterVente/{idV}', [Controller::class, 'updateVente'])->name('ajouterVente.update');
Route::delete('/vente/{idV}', [Controller::class, 'destroyVente'])->name('Vente.destroy');
Route::put('modifVente/{idV}', [Controller::class, 'updateVente']);
Route::get('modifVente/{idV}', [Controller::class, 'updateVente']);
Route::delete('/deleteLigneVente/{id}', [Controller::class, 'deleteLigneVente']);

// Vente 
Route::get('vente', [VenteController::class, 'vente'])->name('vente');



//familleProduitController
Route::get('/familleProduit', [FamilleProduitController::class, 'familleProduit']);
Route::post('/ajouterFamilleProduit', [FamilleProduitController::class, 'ajouterFamilleProduit'])->name('ajouterFamilleProduit');
Route::delete('suppFamilleProduit/{idFamPro}', [FamilleProduitController::class, 'supprimerFamilleProduit']);
Route::put('modifFamilleProduit/{idFamPro}', [FamilleProduitController::class, 'modifierFamilleProduit'])->name('modifierFamilleProduit');

//categorieProduitController
Route::get('/categorieProduit', [CategorieProduitController::class, 'categorieProduit']);
Route::post('/ajouterCategorieProduit', [CategorieProduitController::class, 'ajouterCategorieProduit'])->name('ajouterCategorieProduit');
Route::delete('suppCategorieProduit/{idCatPro}', [CategorieProduitController::class, 'supprimerCategorieProduit']);
Route::put('modifCategorieProduit/{idCatPro}', [CategorieProduitController::class, 'modifierCategorieProduit'])->name('modifierCategorieProduit');

//ProduitController
Route::get('/Produits', [ProduitController::class, 'Produits']);
Route::post('/ajouterProduit', [ProduitController::class, 'ajouterProduit'])->name('ajouterProduit');
Route::delete('suppProduit/{idPro}', [ProduitController::class, 'supprimerProduit']);
Route::put('modifProduit/{idPro}', [ProduitController::class, 'modifierProduit'])->name('modifierProduit');



Route::get('/export-entreprises', [ParamController::class, 'entrepriseExport']);
Route::post('/export-form',  [ParamController::class, 'Export'])->name('export');
// Route::get('/export-entreprises', function () {
//     return Excel::download(new EntreprisesExport, 'entreprises.xlsx');
// });



// DB_USERNAME=hlgs4475_cantinecbox
// DB_PASSWORD=cantinecbox  