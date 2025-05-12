<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategorieFournisseurController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProduitsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\TableauController;
use App\Http\Controllers\ParamController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApprovisionnementController;
use App\Http\Controllers\ExerciceController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\CatClientController;
use App\Http\Controllers\FamilleProduitController;
use App\Http\Controllers\CategorieProduitController;
use App\Http\Controllers\CommandeAchatController;
use App\Http\Controllers\MagasinController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReceptionCmdAchatController;

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

Route::get('/', function () {
     return redirect('/login');
});

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


/* Route::get('/produits', [ProduitsController::class, 'index'])->name('produits');
Route::post('/produits/store', [ProduitsController::class, 'store'])->name('produits.store');
Route::put('/produits/{idP}', [ProduitsController::class, 'update'])->name('produits.update');
Route::delete('/produits/{idP}', [ProduitsController::class, 'destroy'])->name('produits.destroy'); */
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
Route::get('/client', [FournisseurController::class, 'client'])->name('client');


// ApprovisionnementController
// Route::get('/commandeAchat', [ApprovisionnementController::class, 'commandeAchat'])->name('commandeAchat');
// Route::get('/commandeAchat/ajouter', [ApprovisionnementController::class, 'ajoutercommande'])->name('commande.ajouter');
// Route::post('/commandeAchat/store', [ApprovisionnementController::class, 'storeCommande'])->name('commande.store');
// Route::put('/commandeAchat/{idCommande}', [ApprovisionnementController::class, 'updateCommande'])->name('commande.update');
// Route::delete('/commandeAchat/{idCommande}', [ApprovisionnementController::class, 'destroyCommande'])->name('commande.destroy');
// Route::delete('/ligne-commande/{idDetailCom}', [ApprovisionnementController::class, 'deleteLigneCommande'])->name('ligne-commande.destroy');

// CommandeAchatController
// Resource principale sur "commandeAchat" pour coller à votre nom de vue
Route::resource('commandeAchat', CommandeAchatController::class)->middleware('auth');
// Route AJAX pour suppression d'une ligne
Route::delete('commandeAchat/ligne/{id}', [CommandeAchatController::class, 'deleteLigne'])
     ->name('commandeAchat.ligne.destroy');
Route::get('/commande-achat/get-produittva/{idProduit}', [CommandeAchatController::class, 'getProduittva'])
     ->name('commandeAchat.produit.tva');

Route::get('/tableaudebord', [TableauController::class, 'tableaudebord']);
Route::get('/caisses', [Controller::class, 'index'])->name('caisses.index');
Route::post('/caisses', [Controller::class, 'store'])->name('caisses.store');
Route::put('/caisses/{id}', [Controller::class, 'update'])->name('caisses.update');
Route::delete('/caisses/{id}', [Controller::class, 'destroy'])->name('caisses.destroy');


Route::get('/utilisateurs', [ParamController::class, 'utilisateurs'])->name('utilisateurs.utilisateurs');
Route::post('/utilisateurs', [ParamController::class, 'enregistre'])->name('utilisateurs.enregistre');
Route::post('/utilisateurs/{idU}/modifier', [ParamController::class, 'modifie'])->name('utilisateurs.modifie');
Route::delete('/utilisateurs/{idU}/supprimer', [ParamController::class, 'supprime'])->name('utilisateurs.supprime');

Route::get('/parametres/entreprise', [ParamController::class, 'entreprise'])->name('entreprise.entreprise');
Route::post('/parametres/entreprise', [ParamController::class, 'storeEntreprise'])->name('entreprise.storeEntreprise');


// Entreprise
Route::get('/entreprise', [ParamController::class, 'entreprise'])->name('entreprise');
Route::post('/ajouterEntreprise', [ParamController::class, 'ajouterEntreprise'])->name('ajouterEntreprise');
Route::put('modifierEntreprise/{idE}', [ParamController::class, 'modifEntreprise'])->name('modifEntreprise');
Route::delete('suppEntreprise/{idE}', [ParamController::class, 'supprimerEntreprise'])->name('supprimerEntreprise');

Route::get('/roles', [RolesController::class, 'role'])->name('role');
Route::post('/roles/store', [RolesController::class, 'storeRole'])->name('storeRole');
Route::put('/roles/update/{id}', [RolesController::class, 'updateRole'])->name('updateRole');
Route::delete('/roles/delete/{id}', [RolesController::class, 'deleteRole'])->name('deleteRole');

// Reception
// Route::get('/receptions', [Controller::class, 'indexReception'])->name('receptions.index');
// Route::post('/receptions', [Controller::class, 'storeReception'])->name('receptions.store');
// Route::put('/receptions/{idReception}', [Controller::class, 'updateReception'])->name('receptions.update');
// Route::delete('/receptions/{idReception}', [Controller::class, 'destroyReception'])->name('receptions.destroy');

// Route::post('/ajouterCmd', [Controller::class, 'storeCmd'])->name('ajouterCmd.store');
// Route::delete('/commande/{idCmd}', [Controller::class, 'destroyCommande'])->name('commande.destroy');
// Route::put('modifCmd/{idCmd}', [Controller::class, 'updateCmd']);
// Route::get('modifCmd/{idCmd}', [Controller::class, 'updateCmd']);
// Route::delete('/deleteLigneCommande/{id}', [Controller::class, 'deleteLigneCommande']);
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


Route::get('/export-entreprises', [ParamController::class, 'Exporttable']);
Route::get('/get-tables/{databaseName}', [ParamController::class, 'getTables']);
Route::get('/export-form',  [ParamController::class, 'Export'])->name('export');

Route::get('/export-entreprises', [ParamController::class, 'entrepriseExport']);
Route::post('/export-form',  [ParamController::class, 'Export'])->name('export');
// Route::get('/export-entreprises', function () {
//     return Excel::download(new EntreprisesExport, 'entreprises.xlsx');
// });



// DB_USERNAME=hlgs4475_cantinecbox
// DB_PASSWORD=cantinecbox  

// Routes d'authentification (non protégées)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Toutes les autres routes sont protégées par le middleware auth
Route::middleware(['auth'])->group(function () {
     // Route racine
     Route::get('/', function () {
          return redirect('/tableaudebord');
     });

     // Routes du profil
     Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
     Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
     Route::get('/password/change', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
     Route::post('/password/change', [AuthController::class, 'changePassword'])->name('password.update');

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
     Route::put('activerExercice/{id}', [ExerciceController::class, 'activerExercice'])->name('activerExercice');

     // Categories
     Route::get('/categories', [CategoriesController::class, 'index'])->name('categories');
     Route::delete('/categories/{id}', [CategoriesController::class, 'destroy'])->name('categories.destroy');
     Route::put('/categories/{id}', [CategoriesController::class, 'update'])->name('categories.update');
     Route::get('/categories/edit/{id}', [CategoriesController::class, 'edit'])->name('categories.edit');
     Route::post('/categories/store', [CategoriesController::class, 'store'])->name('categories.store');

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
     Route::put('modifMagasin/{id}', [MagasinController::class, 'updateMagasin'])->name('magasins.updateMagasin');

     // CommandeAchat
     Route::resource('commandeAchat', CommandeAchatController::class);
     Route::delete('commandeAchat/ligne/{id}', [CommandeAchatController::class, 'deleteLigne'])
          ->name('commandeAchat.ligne.destroy');
     Route::get('/commande-achat/get-produittva/{idProduit}', [CommandeAchatController::class, 'getProduittva'])
          ->name('commandeAchat.produit.tva');

     // Tableau de bord
     Route::get('/tableaudebord', [TableauController::class, 'tableaudebord']);

     // Caisses
     Route::get('/caisses', [Controller::class, 'index'])->name('caisses.index');
     Route::post('/caisses', [Controller::class, 'store'])->name('caisses.store');
     Route::put('/caisses/{id}', [Controller::class, 'update'])->name('caisses.update');
     Route::delete('/caisses/{id}', [Controller::class, 'destroy'])->name('caisses.destroy');

     // Utilisateurs
     Route::get('/utilisateurs', [ParamController::class, 'utilisateurs'])->name('utilisateurs.utilisateurs');
     Route::post('/utilisateurs', [ParamController::class, 'enregistre'])->name('utilisateurs.enregistre');
     Route::post('/utilisateurs/{idU}/modifier', [ParamController::class, 'modifie'])->name('utilisateurs.modifie');
     Route::delete('/utilisateurs/{idU}/supprimer', [ParamController::class, 'supprime'])->name('utilisateurs.supprime');

     // Entreprise
     Route::get('/entreprise', [ParamController::class, 'entreprise'])->name('entreprise');
     Route::post('/ajouterEntreprise', [ParamController::class, 'ajouterEntreprise'])->name('ajouterEntreprise');
     Route::put('modifierEntreprise/{idE}', [ParamController::class, 'modifEntreprise'])->name('modifEntreprise');
     Route::delete('suppEntreprise/{idE}', [ParamController::class, 'supprimerEntreprise'])->name('supprimerEntreprise');

     // Roles
     Route::get('/roles', [RolesController::class, 'role'])->name('role');
     Route::post('/roles/store', [RolesController::class, 'storeRole'])->name('storeRole');
     Route::put('/roles/update/{id}', [RolesController::class, 'updateRole'])->name('updateRole');
     Route::delete('/roles/delete/{id}', [RolesController::class, 'deleteRole'])->name('deleteRole');

     // Vente
     Route::post('/ajouterVente', [Controller::class, 'storeVente'])->name('ajouterVente.store');
     Route::delete('/vente/{idV}', [Controller::class, 'destroyVente'])->name('Vente.destroy');
     Route::put('modifVente/{idV}', [Controller::class, 'updateVente']);
     Route::get('modifVente/{idV}', [Controller::class, 'updateVente']);
     Route::delete('/deleteLigneVente/{id}', [Controller::class, 'deleteLigneVente']);
     Route::get('vente', [VenteController::class, 'vente'])->name('vente');

     // Produits
     Route::get('/familleProduit', [FamilleProduitController::class, 'familleProduit']);
     Route::post('/ajouterFamilleProduit', [FamilleProduitController::class, 'ajouterFamilleProduit'])->name('ajouterFamilleProduit');
     Route::delete('suppFamilleProduit/{idFamPro}', [FamilleProduitController::class, 'supprimerFamilleProduit']);
     Route::put('modifFamilleProduit/{idFamPro}', [FamilleProduitController::class, 'modifierFamilleProduit'])->name('modifierFamilleProduit');

     Route::get('/categorieProduit', [CategorieProduitController::class, 'categorieProduit']);
     Route::post('/ajouterCategorieProduit', [CategorieProduitController::class, 'ajouterCategorieProduit'])->name('ajouterCategorieProduit');
     Route::delete('suppCategorieProduit/{idCatPro}', [CategorieProduitController::class, 'supprimerCategorieProduit']);
     Route::put('modifCategorieProduit/{idCatPro}', [CategorieProduitController::class, 'modifierCategorieProduit'])->name('modifierCategorieProduit');

     Route::get('/Produits', [ProduitController::class, 'Produits']);
     Route::post('/ajouterProduit', [ProduitController::class, 'ajouterProduit'])->name('ajouterProduit');
     Route::delete('suppProduit/{idPro}', [ProduitController::class, 'supprimerProduit']);
     Route::put('modifProduit/{idPro}', [ProduitController::class, 'modifierProduit'])->name('modifierProduit');

     // Export
     Route::get('/export-entreprises', [ParamController::class, 'entrepriseExport']);
     Route::post('/export-form', [ParamController::class, 'Export'])->name('export');

     // Reception
     Route::resource('receptions', ReceptionCmdAchatController::class);
     Route::get('/receptions/commande-details/{idCommande}', [ReceptionCmdAchatController::class, 'getCommandeDetails'])->name('receptions.commande-details');
});