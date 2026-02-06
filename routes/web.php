<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategorieFournisseurController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ClientController;
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
use App\Http\Controllers\TestController;
use App\Http\Controllers\ModePaiementController;
use App\Http\Controllers\InventaireController;
use App\Http\Controllers\FermetureController;
use App\Http\Controllers\ProduitStockController;
use App\Http\Controllers\TransfertMagasinController;
use App\Http\Controllers\ProformatController;
use App\Http\Controllers\MenuPermissionController;
use App\Http\Controllers\CategorieTarifaireController;

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

Route::get('/test', [TestController::class, 'index'])->name('test');

Route::get('/', function () {
     return redirect('/login');
});


// Route::get('/export-entreprises', [ParamController::class, 'Exporttable']);
// Route::get('/get-tables/{databaseName}', [ParamController::class, 'getTables']);
// Route::get('/export-form',  [ParamController::class, 'Export'])->name('export');

// Route::get('/export-entreprises', [ParamController::class, 'entrepriseExport']);
// Route::post('/export-form',  [ParamController::class, 'Export'])->name('export');
// Route::get('/export-entreprises', function () {
//     return Excel::download(new EntreprisesExport, 'entreprises.xlsx');
// });



// DB_USERNAME=hlgs4475_cantinecbox
// DB_PASSWORD=cantinecbox  

// Routes d'authentification (non protégées)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');






// routes tableau de bord 
// Route::get('/tableaudebord', [TableauController::class, 'tableaudebord'])
//     ->middleware(['auth', 'role:Administrateur,Vendeus/Caissier,Magasinier'])
//     ->name('tableaudebord');


// Route tableau de bord et Profil commune a tous les roles

// Route::middleware(['auth', 'role:Administrateur,Magasinier,Vendeus/Caissier'])->group(function () {
     Route::get('/tableaudebord', [TableauController::class, 'tableaudebord'])->name('tableaudebord')->middleware('auth','can_menu:tableaudebord,view');
    
     // profile, password, etc.
     Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile')->middleware('auth','can_menu:profile,view');
     Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update')->middleware('auth','can_menu:profile,edit');
     Route::get('/password/change', [AuthController::class, 'showChangePasswordForm'])->name('password.change')->middleware('auth','can_menu:profile,view');
     Route::post('/password/change', [AuthController::class, 'changePassword'])->name('password.update')->middleware('auth','can_menu:profile,edit');
// });


// Route commune a l'admin et au magasinier 
// Route::middleware(['auth', 'role:Administrateur,Magasinier'])->group(function () {
     //Cayegorie Fournisseur
     Route::get('/categoriesFournisseur', [CategorieFournisseurController::class, 'index'])->name('categoriesF')->middleware('auth','can_menu:categoriesF,view');
     Route::post('/categorieFournisseur/store', [CategorieFournisseurController::class, 'store'])->name('categoriesF.store')->middleware('auth','can_menu:categoriesF,create');
     Route::delete('/categoriesFournisseur/{id}', [CategorieFournisseurController::class, 'destroy'])->name('categoriesF.destroy')->middleware('auth','can_menu:categoriesF,delete');
     Route::put('/categoriesFournisseur/{id}', [CategorieFournisseurController::class, 'update'])->name('categoriesF.update')->middleware('auth','can_menu:categoriesF,edit');
     Route::get('/categoriesFournisseur/edit/{id}', [CategorieFournisseurController::class, 'edit'])->name('categoriesF.edit')->middleware('auth','can_menu:categoriesF,view');

     //Fournisseur
     Route::get('/fournisseur', [FournisseurController::class, 'fournisseur'])->name('fournisseur')->middleware('auth','can_menu:fournisseur,view');
     Route::post('/ajouterFournisseur', [FournisseurController::class, 'ajouterFournisseur'])->name('fournisseurs.ajouterFournisseur')->middleware('auth','can_menu:fournisseur,create');
     Route::delete('suppFournisseur/{id}', [FournisseurController::class, 'deleteFournisseur'])->middleware('auth','can_menu:fournisseur,delete');
     Route::put('modifFournisseur/{id}', [FournisseurController::class, 'updateFournisseur'])->name('fournisseur.update')->middleware('auth','can_menu:fournisseur,edit');

     // Magasin
     Route::get('/magasins', [MagasinController::class, 'index'])->name('magasins')->middleware('auth','can_menu:magasins,view');
     Route::post('/ajouterMagasin', [MagasinController::class, 'ajouterMagasin'])->name('magasins.ajouterMagasin')->middleware('auth','can_menu:magasins,create');
     Route::delete('suppMagasin/{id}', [MagasinController::class, 'destroy'])->name('magasins.destroy')->middleware('auth','can_menu:magasins,delete');
     Route::post('addProduct/{idMag}', [MagasinController::class, 'addProduct'])->name('magasins.addProduct')->middleware('auth','can_menu:magasins,create');
     Route::put('modifMagasin/{id}', [MagasinController::class, 'updateMagasin'])->name('magasins.updateMagasin')->middleware('auth','can_menu:magasins,edit');

      // CommandeAchat
     Route::resource('commandeAchat', CommandeAchatController::class);
     Route::delete('commandeAchat/ligne/{id}', [CommandeAchatController::class, 'deleteLigne'])
          ->name('commandeAchat.ligne.destroy');
     Route::get('/commande-achat/get-produittva/{idProduit}', [CommandeAchatController::class, 'getProduittva'])
          ->name('commandeAchat.produit.tva');
     /* Route::get('/get-nouvelle-reference', [CommandeAchatController::class, 'getNouvelleReference']);           */
     Route::get('/magasin/{idMag}/produits',[CommandeAchatController::class, 'getProduitsByMagasin']);


      // Produits
     Route::get('/familleProduit', [FamilleProduitController::class, 'familleProduit'])->name('familleProduit')->middleware('auth','can_menu:familleProduit,view');
     Route::post('/ajouterFamilleProduit', [FamilleProduitController::class, 'ajouterFamilleProduit'])->name('ajouterFamilleProduit')->middleware('auth','can_menu:familleProduit,create');
     Route::delete('suppFamilleProduit/{idFamPro}', [FamilleProduitController::class, 'supprimerFamilleProduit'])->middleware('auth','can_menu:familleProduit,delete');
     Route::put('modifFamilleProduit/{idFamPro}', [FamilleProduitController::class, 'modifierFamilleProduit'])->name('modifierFamilleProduit')->middleware('auth','can_menu:familleProduit,edit');

     Route::get('/categorieProduit', [CategorieProduitController::class, 'categorieProduit'])->name('categorieProduit')->middleware('auth','can_menu:categorieProduit,view');
     Route::post('/ajouterCategorieProduit', [CategorieProduitController::class, 'ajouterCategorieProduit'])->name('ajouterCategorieProduit')->middleware('auth','can_menu:categorieProduit,create');
     Route::delete('suppCategorieProduit/{idCatPro}', [CategorieProduitController::class, 'supprimerCategorieProduit'])->middleware('auth','can_menu:categorieProduit,delete');
     Route::put('modifCategorieProduit/{idCatPro}', [CategorieProduitController::class, 'modifierCategorieProduit'])->name('modifierCategorieProduit')->middleware('auth','can_menu:categorieProduit,edit');


     Route::get('/Produits', [ProduitController::class, 'Produits'])->name('Produits')->middleware('auth','can_menu:Produits,view');
     Route::post('/ajouterProduit', [ProduitController::class, 'ajouterProduit'])->name('ajouterProduit')->middleware('auth','can_menu:Produits,create');
     Route::delete('suppProduit/{idPro}', [ProduitController::class, 'supprimerProduit'])->middleware('auth','can_menu:Produits,delete');
     Route::put('modifProduit/{idPro}', [ProduitController::class, 'modifierProduit'])->name('modifierProduit')->middleware('auth','can_menu:Produits,edit');
     Route::get('/produit/{idPro}/detail', [ProduitController::class, 'detail'])
          ->name('produit.detail');

          
     // inventaire
     // Route::get('/inventaire', [InventaireController::class, 'inventaire']);
     Route::get('/inventaires', [InventaireController::class, 'index'])->name('inventaires')->middleware('auth','can_menu:inventaires,view');
     Route::post('/inventaires', [InventaireController::class, 'search'])->name('inventaires.search')->middleware('auth','can_menu:inventaires,create');
     // Route::post('/ajouterProduit', [ProduitController::class, 'ajouterProduit'])->name('ajouterProduit');


     // Reception
     Route::resource('receptions', ReceptionCmdAchatController::class);
     Route::get('/receptions/commande-details/{idCommande}', [ReceptionCmdAchatController::class, 'getCommandeDetails'])
          ->name('receptions.commande-details');
     Route::get('/receptions/commande-details/{idCommande}', [ReceptionCmdAchatController::class, 'getCommandeDetails'])->name('receptions.commande-details');


     // Gestion des stocks
     Route::get('/consulter-stocks', [ProduitStockController::class, 'consulterStocks'])->name('consulterStocks')->middleware('auth','can_menu:consulterStocks,view');
     Route::get('/ajuster-stocks', [ProduitStockController::class, 'ajusterStocks'])->name('stocks.ajuster')->middleware('auth','can_menu:consulterStocks,view');
     Route::post('/ajuster-stock', [ProduitStockController::class, 'ajusterStock'])->name('stocks.ajuster.stock')->middleware('auth','can_menu:consulterStocks,create');
     Route::get('/stock-details/{idStocke}', [ProduitStockController::class, 'getStockDetails'])->name('stocks.details')->middleware('auth','can_menu:consulterStocks,view');
     Route::get('/produit-image/{idPro}', [ProduitStockController::class, 'getProduitImage'])->name('produits.image')->middleware('auth','can_menu:consulterStocks,view');
     
     // Transferts entre magasins
     Route::get('/transferts', [TransfertMagasinController::class, 'index'])->name('transferts')->middleware('auth','can_menu:transferts,view');
     Route::post('/transferts', [TransfertMagasinController::class, 'store'])->name('transferts.store')->middleware('auth','can_menu:transferts,create');
     Route::get('/transferts/{idTransMag}', [TransfertMagasinController::class, 'show'])->name('transferts.show')->middleware('auth','can_menu:transferts,view');
     Route::get('/transferts/{idTransMag}/details', [TransfertMagasinController::class, 'showDetails'])->name('transferts.details')->middleware('auth','can_menu:transferts,view');
     Route::get('/transferts/stocks/{idMag}', [TransfertMagasinController::class, 'getStocksByMagasin'])->name('transferts.stocks.magasin')->middleware('auth','can_menu:transferts,view');

     // Pour le bail de fermeture de journée
     Route::get('/fermetures', [FermetureController::class, 'index'])
         ->name('fermetures')->middleware('auth','can_menu:fermetures,view');
     // Route::get('/fermeture-journee', [FermetureController::class, 'store'])
     //     ->name('fermeture.journee');
     Route::post('/fermeture-journee', [FermetureController::class, 'store'])
         ->name('fermeture.journee')->middleware('auth','can_menu:fermetures,create');



// });


// Route commune au roles administrateur et Vendeus/Caissier
// Route::middleware(['auth', 'role:Administrateur,Vendeus/Caissier'])->group(function () {
     // Ventes et factures
     Route::post('/ajouterVente', [VenteController::class, 'storeVente'])->name('ajouterVente.store')->middleware('auth','can_menu:ventes,create');
     Route::post('/deletevente/{idFacture}', [VenteController::class, 'deletevente'])->name('deletevente')->middleware('auth','can_menu:ventes,delete');
     Route::put('modifVente/{idV}', [VenteController::class, 'updateVente'])->middleware('auth','can_menu:ventes,edit');
     Route::get('modifVente/{idV}', [VenteController::class, 'updateVente'])->middleware('auth','can_menu:ventes,view');
     Route::delete('/deleteLigneVente/{id}', [VenteController::class, 'deleteLigneVente'])->middleware('auth','can_menu:ventes,view');
     Route::get('ventes', [VenteController::class, 'vente'])->name('ventes')->middleware('auth','can_menu:ventes,view');

     Route::get('facturation', [VenteController::class, 'facturation'])->name('facturation')->middleware('auth','can_menu:facturation,view');
     Route::get('/get-nouvelle-reference', [VenteController::class, 'getNouvelleReference'])->middleware('auth','can_menu:facturation,view');
     Route::get('/get-produit-info/{id}', [VenteController::class, 'getProduitInfo'])->middleware('auth','can_menu:facturation,view');
     Route::get('duplicatafacture/{id}', [VenteController::class, 'duplicatafacture'])->name('duplicatafacture')->middleware('auth','can_menu:facturation,view');

     // proforma
     Route::get('proforma', [ProformatController::class, 'index'])->name('proformat')->middleware('auth','can_menu:proformat,view');
     Route::get('duplicataproforma/{idProforma}', [ProformatController::class, 'duplicataproformat'])->name('duplicataproforma')->middleware('auth','can_menu:proformat,view');
     Route::post('ajouterproforma', [ProformatController::class, 'storeProforma'])->name('storeProforma')->middleware('auth','can_menu:proformat,create');
     Route::post('/deleteproforma/{idProforma}', [ProformatController::class, 'deleteProforma'])->name('deleteProforma')->middleware('auth','can_menu:proformat,delete');


     // clientcontroller
     Route::get('/clients', [ClientController::class, 'index'])->name('clients')->middleware('auth','can_menu:clients,view');
     Route::post('/clients', [ClientController::class, 'store'])->name('clients.store')->middleware('auth','can_menu:clients,create');
     Route::put('/clients/{idC}', [ClientController::class, 'update'])->name('clients.update')->middleware('auth','can_menu:clients,edit');
     Route::delete('/clients/{idC}', [ClientController::class, 'destroy'])->name('clients.destroy')->middleware('auth','can_menu:clients,delete');
// });


// Routes pour le role Administrateur seul 
// Route::middleware(['auth', 'role:Administrateur'])->group(function () {


// });

//   les routes de l'admin

   
     // Export
     Route::get('/export-entreprises', [ParamController::class, 'entrepriseExport']);
     Route::post('/export-form', [ParamController::class, 'Export'])->name('export');


     Route::middleware(['auth', 'role:Administrateur'])->group(function () {

          Route::get('/menu-permissions', [MenuPermissionController::class, 'index'])->name('menupermissions');
          Route::post('/admin/menu-permissions', [MenuPermissionController::class, 'updatePermissions'])->name('menupermissions.update');

          // Modes de paiement
          Route::get('/modepaiement', [ModePaiementController::class, 'index'])->name('modepaiement');
          Route::post('/modepaiement', [ModePaiementController::class, 'store'])->name('modepaiement.store');
          Route::put('/modepaiement/{idModPaie}', [ModePaiementController::class, 'update'])->name('modepaiement.update');
          Route::delete('/modepaiement/{idModPaie}', [ModePaiementController::class, 'destroy'])->name('modepaiement.destroy');


          // Roles
          Route::get('/roles', [RolesController::class, 'role'])->name('role');
          Route::post('/roles/store', [RolesController::class, 'storeRole'])->name('storeRole');
          Route::put('/roles/update/{id}', [RolesController::class, 'updateRole'])->name('updateRole');
          Route::delete('/roles/delete/{id}', [RolesController::class, 'deleteRole'])->name('deleteRole');

          // Entreprise
          Route::get('/entreprise', [ParamController::class, 'entreprise'])->name('entreprise');
          Route::post('/ajouterEntreprise', [ParamController::class, 'ajouterEntreprise'])->name('ajouterEntreprise');
          Route::put('modifierEntreprise/{idE}', [ParamController::class, 'modifEntreprise'])->name('modifEntreprise');
          Route::delete('suppEntreprise/{idE}', [ParamController::class, 'supprimerEntreprise'])->name('supprimerEntreprise');

          // Caisses
          Route::get('/caisses', [Controller::class, 'index'])->name('caisses');
          Route::post('/caisses', [Controller::class, 'store'])->name('caisses.store');
          Route::put('/caisses/{id}', [Controller::class, 'update'])->name('caisses.update');
          Route::delete('/caisses/{id}', [Controller::class, 'destroy'])->name('caisses.destroy');

          // Utilisateurs
          Route::get('/utilisateurs', [ParamController::class, 'utilisateurs'])->name('utilisateurs');
          Route::post('/utilisateurs', [ParamController::class, 'enregistre'])->name('utilisateurs.enregistre');
          Route::post('/utilisateurs/{idU}/modifier', [ParamController::class, 'modifie'])->name('utilisateurs.modifie');
          Route::delete('/utilisateurs/{idU}/supprimer', [ParamController::class, 'supprime'])->name('utilisateurs.supprime');


          // Exercicecontroller
          Route::get('/exercice', [ExerciceController::class, 'exercice'])->name('exercice');
          Route::post('/ajouterExercice', [ExerciceController::class, 'ajouterExercice'])->name('ajouterExercice');
          Route::put('activerExercice/{id}', [ExerciceController::class, 'activerExercice'])->name('activerExercice');


          // Categorie Tarifaire
          Route::get('/categorie_tarifaire', [CategorieTarifaireController::class, 'CategorieTarifaire'])->name('CategorieTarifaire');
          Route::post('/categorie_tarifaire_create', [CategorieTarifaireController::class, 'CreateCategorieTarifaire'])->name('CreateCategorieTarifaire');
          Route::put('/categorie_tarifaire_edit/{id}', [CategorieTarifaireController::class, 'EditCategorieTarifaire'])->name('EditCategorieTarifaire');
          Route::post('/categorie_tarifaire_activate/{id}', [CategorieTarifaireController::class, 'ActiverouDeasactiverCategorieTarifaire'])->name('DeleteCategorieTarifaire');

});

