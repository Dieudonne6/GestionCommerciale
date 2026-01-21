<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\Magasin;
use App\Models\Entreprise;
use App\Models\Produit;
use App\Models\Stocke;
use App\Models\CategorieProduit;
use App\Models\FamilleProduit;

class MagasinController extends Controller
{
    // Afficher la liste des magasins
    public function index()
    {
        // $magasins = Magasin::with('entreprise')->get();
        $entreprises = Entreprise::all();
        $produits = Produit::all(); // Pour le modal d'ajout de produit
        $allCategorieProduits = CategorieProduit::get();
        $allFamilleProduits = FamilleProduit::get();

        $user = auth()->user();
        $userId = $user->idU;
        $entrepriseId = $user->idE;
        // dd($entrepriseId);
        $magasins = Magasin::where('idE', $entrepriseId)
            ->with('entreprise')
            ->get();


        return view('pages.ProduitStock.magasins', compact('magasins', 'produits', 'entreprises', 'allCategorieProduits', 'allFamilleProduits','entrepriseId'));
    }

    public function ajouterMagasin(Request $request)
    {
        // Validation des données
        $validator = \Validator::make($request->all(), [
            'libelle' => 'required|string|max:250',
            'codeMagasin' => 'required|string|min:3|max:255|unique:magasins,codeMagasin',
            'Adresse' => 'required|string|min:5|max:255',
            'idE' => 'required',
        ], [
            'libelle.required' => 'L\'IFU est obligatoire.',
            'codeMagasin.unique' => 'Cet Magasin existe déjà.',
            'codeMagasin.required' => 'Un code est requis.',
            'Adresse.required' => 'L\'adresse est obligatoire.',
            'idE.required' => 'Ce champ est obligatoire.',
        ]);

        // Vérifier si la validation a échoué
        if ($validator->fails()) {
            return redirect()->route('magasins')
                ->withErrors($validator)
                ->withInput()
                ->with('showAddMagasinModal', true);
        }

        // Création du fournisseur
        
            $magasins = new Magasin();
            $magasins->libelle = $request->input('libelle');
            $magasins->codeMagasin = $request->input('codeMagasin');
            $magasins->Adresse = $request->input('Adresse');
            $magasins->idE = $request->input('idE');
            $magasins->save();

            return redirect()->route('magasins')->with('status', 'Magasin ajouté avec succès !');
    } 
    // Afficher les détails d'un magasin (produits associés)

    // Ajouter un produit au magasin
    public function addProduct(Request $request, $idMag)
    {
        // Vérifier si le produit existe déjà
        $produit = Produit::where('libelle', $request->libelle)->first();

        // dd($request->all(), $idMag);

        $validator = \Validator::make($request->all(), [
            'libelle' => 'required|string|max:255',
            'qteStocke' => 'nullable|integer|min:0',
            'idCatPro' => 'required',
            'idFamPro' => 'required',
        ], [
            'idCatPro.required' => 'Ce champ est obligatoire.',
            'idFamPro.required' => 'Ce champ est obligatoire.',
        ]);

        // Vérifier si la validation a échoué
        if ($validator->fails()) {
            session()->flash('showModifyMagasinModal', $idMag);
            return redirect()->route('magasins')->withErrors($validator);
        }

        // dd('jojo');
        if (!$produit) {
            // Création du produit s'il n'existe pas
            $produit = Produit::create([
                'libelle' => $request->libelle,
                'prix' => $request->prix,
                'desc' => $request->desc,
                'stockAlert' => $request->stockAlert,
                'stockMinimum' => $request->stockMinimum,
                'idCatPro' => $request->idCatPro,
                'idFamPro' => $request->idFamPro,
                'image' => file_get_contents($request->file('image')->getRealPath()),
            ]);
        }

        // Vérifier si ce produit est déjà en stock dans ce magasin
        $stock = Stocke::where('idPro', $produit->idPro)
                       ->where('idMag', $idMag)
                       ->first();

        if ($stock) {
            // Mise à jour de la quantité existante
            $stock->qteStocke += $request->qteStocke;
            $stock->save();
        } else {
            // Ajouter un nouvel enregistrement de stock
            Stocke::create([
                'idPro' => $produit->idPro,
                'idMag' => $idMag,
                'qteStocke' => $request->qteStocke,
                'CUMP' => 0, // Mettre à jour CUMP si nécessaire
            ]);
        }

        return redirect()->back()->with('status', 'Produit ajouté au stock avec succès !');
    } 

    // Supprimer un magasin
    public function destroy($id)
    {
        $magasin = Magasin::findOrFail($id);

        // $produits = Produit::where('idPro', )

        DB::transaction(function () use ($magasin) {
            // Supprimer les produits liés via stocke
            $magasin->stocke()->each(function ($stock) {
                $stock->produit()->delete();
            });

            // Supprimer le magasin
            $magasin->delete();
        });
        // $magasin->delete();

        return redirect()->route('magasins')->with('status', 'Magasin et produit associés supprimés avec succès.');
    }

    // Mettre à jour un magasin
/*     public function update(Request $request, $id)
    {
        $magasin = Magasin::findOrFail($id);

        $request->validate([
            'libelle' => 'required|string|min:5|max:255',
            'codeMagasin' => 'required|string|min:5|max:255|unique:magasins,codeMagasin,' . $id . ',idMag',
            'Adresse' => 'required|string|min:5|max:255',
        ]);

        $magasin->update($request->all());

        return redirect()->route('pages.ProduitStock.magasins')->with('success', 'Magasin mis à jour avec succès.');
    } */


    //         public function savepaiementetinscriptioncontrat(Request $request)
    // {
    //     // dd("crercontratetpaiement");
    //     // $data = $request->validated();
    //     // recuperer les donne entrer par l'utilisateur
    //     $classes = $request->input('classes');
    //     $eleveId = $request->input('matricules');
    //     $montant = $request->input('montant');
    //     $montantinteger = intval($montant);
    //     $idUserContrat = $request->input('id_usercontrat');
    //     // $dateContrat = $request->input('date');
    //     // dd($idUserContrat);
    //     $InfoUtilisateurConnecter =  User::where('id', $idUserContrat)->first();
    //     $id_usercontrat =  $InfoUtilisateurConnecter->id;
    //     $id_usercontratInt = intval($id_usercontrat);


    //     $moisCoches = $request->input('moiscontrat');
    //     $montantmoiscontrat = $request->input('montantcontrat');
    //     $montanttotal = $request->input('montanttotal');
    //     $datepaiementcontrat = $request->input('date');
    //     $montantParMoisReel = $request->input('montantcontratReel');
    //     $montantParMoisReelInt = intval($montantParMoisReel);
    //     $id_usercontrat = Session::get('id_usercontrat');
    //     // $dateContrat = $request->input('date');
    //     // Récupérer la date avec l'heure depuis la requête
    //     $dateContrt = $request->input('datePaiement');

    //     $anneeActuelle = date('Y');

    //     $infoParamContrat = Paramcontrat::first();
    //     $debutAnneeEnCours = $infoParamContrat->anneencours_paramcontrat;
    //     $anneeSuivante = $debutAnneeEnCours + 1;
    //     $anneeScolaireEnCours = $debutAnneeEnCours . '-' . $anneeSuivante;

    //     // Convertir en objet Carbon
    //     $dateContratt = Carbon::parse($dateContrt);

    //     // Formater la date pour l'affichage
    //     $dateContrat = $dateContratt->format('Y-m-d H:i:s');
    //     // dd($dateContrt);


    //     // Si la date n'est pas spécifiée, utiliser la date du jour
    //     // if (empty($dateContrat)) {
    //     //     $dateContrat = date('Y-m-d H:i:s');
    //     // }
    //     // Trouver l'élève en fonction de la classe (CODECLAS)
    //     $elevy = Eleve::where('MATRICULE', $eleveId)->get();

    //     // Si la date n'est pas spécifiée, utiliser la date du jour
    //     // if (empty($dateContrat)) {
    //     //     $dateContrat = date('Y-m-d');
    //     // }

    //     // Trouver l'élève en fonction de la classe (CODECLAS)
    //     $elevy = Eleve::where('MATRICULE', $eleveId)->get();

    //     $nom = Eleve::where('MATRICULE', $eleveId)->value('NOM');
    //     $prenom = Eleve::where('MATRICULE', $eleveId)->value('PRENOM');
    //     $elevyo = $nom . ' ' . $prenom;


    //     // dd($moisCoches);
    //     // Array des noms des mois
    //     $nomsMoisCoches = [];
    //     if (is_array($moisCoches)) {

    //         // Parcourir les ID des mois cochés et obtenir leur nom correspondant
    //         foreach ($moisCoches as $id_moiscontrat) {
    //             // Ici, vous pouvez récupérer le nom du mois à partir de votre modèle Mois
    //             $mois = Moiscontrat::where('id_moiscontrat', $id_moiscontrat)->first();

    //             // Vérifiez si le mois existe
    //             if ($mois) {
    //                 // Ajouter le nom du mois à l'array
    //                 $nomsMoisCoches[] = $mois->nom_moiscontrat;
    //             }
    //         }
    //     }

    //     $moisConcatenes = implode(',', $nomsMoisCoches);

    //     $parametrefacture = Params2::first();
    //     $ifuentreprise = $parametrefacture->ifu;
    //     $tokenentreprise = $parametrefacture->token;
    //     $taxe = $parametrefacture->taxe;
    //     $type = $parametrefacture->typefacture;

    //     $parametreetab = Params2::first();

    //     $moisavecvirg = implode(',', $nomsMoisCoches);
    //     $toutmoiscontrat = $moisavecvirg;

    //     // dd($moisavecvirg);
    //     $items = []; // Initialiser un tableau vide pour les éléments

    //     // AJOUT D’UNE LIGNE FIXE POUR INSCRIPTION
    //     $items[] = [
    //         'name'      => 'Frais cantine pour inscription',
    //         'price'     => intval($montantinteger),
    //         'quantity'  => 1,
    //         'taxGroup'  => $taxe,
    //     ];

    //     foreach ($nomsMoisCoches as $idmois => $mois) {
    //         $items[] = [
    //             'name' => 'Frais cantine pour : ' . $mois, // Pas besoin de $$ pour une variable
    //             'price' => intval($montantmoiscontrat),
    //             'quantity' => 1,
    //             'taxGroup' => $taxe,
    //         ];

    //         // Définir $montantAPayer par défaut pour tous les mois
    //         if (in_array($mois, ['Decembre', 'Septembre', 'Avril'])) {
    //             // Montant spécifique pour certains mois
    //             switch ($mois) {
    //                 case 'Decembre':
    //                     $montantAPayer = $montantParMoisReelInt;
    //                     break;
    //                 case 'Septembre':
    //                     $montantAPayer = $montantParMoisReelInt;
    //                     break;
    //                 case 'Avril':
    //                     $montantAPayer = $montantParMoisReelInt;
    //                     break;
    //             }
    //         } else {
    //             // Montant par défaut pour les autres mois
    //             $montantAPayer = $montantParMoisReelInt;
    //         }

    //         // Calculer le total des montants
    //         $totalMontantinfoFacture = 0;

    //         // Si $totalMontantinfoFacture est null, le remplacer par 0
    //         $totalMontantinfoFacture = $totalMontantinfoFacture ?? 0;

    //         // Calculer la somme des montants
    //         $sommeDesMontant = $totalMontantinfoFacture + $montantmoiscontrat;
    //         // dd($montantAPayer);

    //         // Déterminer si le mois peut être sauvegardé
    //         if ($sommeDesMontant < $montantAPayer) {
    //             $saveMois = 1;
    //         } else {
    //             $saveMois = 0;
    //         }
    //     }
    //     // dd($items);
    //     // Préparez les données JSON pour l'API
    //     $jsonData = json_encode([
    //         "ifu" => $ifuentreprise, // ici on doit rendre la valeur de l'ifu dynamique
    //         // "aib" => "A",
    //         "type" => $type,
    //         "items" => $items,

    //         "client" => [
    //             // "ifu" => '',
    //             "name" =>  $elevyo,
    //             // "contact" => "string",
    //             // "address"=> "string"
    //         ],
    //         "operator" => [
    //             "name" => " CRYSTAL SERVICE INFO (TONY ABAMAN FIRMIN)"
    //         ],
    //         "payment" => [
    //             [
    //                 "name" => "ESPECES",
    //                 "amount" => intval($montanttotal + $montantinteger)
    //             ]
    //         ],
    //     ]);

    //     $apiUrl = 'https://developper.impots.bj/sygmef-emcf/api/invoice';

    //     $token = $tokenentreprise;

    //     // Effectuez la requête POST à l'API
    //     // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     $ch = curl_init($apiUrl);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Content-Type: application/json',
    //         'Authorization: Bearer ' . $token
    //     ));
    //     curl_setopt($ch, CURLOPT_CAINFO, storage_path('certificates/cacert.pem'));

    //     // Exécutez la requête cURL et récupérez la réponse
    //     $response = curl_exec($ch);

    //     // Vérifiez les erreurs de cURL
    //     if (curl_errno($ch)) {
    //         // echo 'Erreur cURL : ' . curl_error($ch);
    //         return back()->with('erreur', 'Erreur curl , mauvaise connexion a l\'API');
    //     }

    //     // Fermez la session cURL
    //     curl_close($ch);

    //     // Affichez la réponse de l'API
    //     $decodedResponse = json_decode($response, true);
    //     // dd($decodedResponse);


    //     // Vérifiez si l'UID est présent dans la réponse
    //     if (isset($decodedResponse['uid'])) {
    //         // L'UID de la demande
    //         $uid = $decodedResponse['uid'];
    //         // $taxb = 0.18;

    //         // Affichez l'UID
    //         // echo "L'UID de la demande est : $uid";


    //         // -------------------------------
    //         //  RECUPERATION DE LA FACTURE PAR SON UID
    //         // -------------------------------

    //         // Définissez l'URL de l'API de confirmation de facture
    //         $recuperationUrl = 'https://developper.impots.bj/sygmef-emcf/api/invoice/' . $uid;

    //         // Configuration de la requête cURL pour la confirmation
    //         $chRecuperation = curl_init($recuperationUrl);
    //         curl_setopt($chRecuperation, CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($chRecuperation, CURLOPT_CUSTOMREQUEST, 'GET');
    //         curl_setopt($chRecuperation, CURLOPT_HTTPHEADER, [
    //             'Authorization: Bearer ' . $token,
    //             'Content-Length: 0'
    //         ]);
    //         curl_setopt($chRecuperation, CURLOPT_CAINFO, storage_path('certificates/cacert.pem'));

    //         // Exécutez la requête cURL pour la confirmation
    //         $responseRecuperation = curl_exec($chRecuperation);
    //         // dd($responseRecuperation);
    //         // Vérifiez les erreurs de cURL pour la confirmation


    //         // Fermez la session cURL pour la confirmation
    //         curl_close($chRecuperation);

    //         // Convertissez la réponse JSON en tableau associatif PHP
    //         $decodedDonneFacture = json_decode($responseRecuperation, true);
    //         // dd($decodedDonneFacture);

    //         $facturedetaille = json_decode($jsonData, true);
    //         $ifuEcoleFacture = $decodedDonneFacture['ifu'];
    //         $itemFacture = $decodedDonneFacture['items'];
    //         $jsonItem = json_encode($itemFacture);
    //         $doneeDetailleItemFacture = $itemFacture['0'];
    //         $nameItemFacture = $doneeDetailleItemFacture['name'];
    //         $prixTotalItemFacture = $doneeDetailleItemFacture['price'];
    //         $quantityItemFacture = $doneeDetailleItemFacture['quantity'];
    //         $taxGroupItemFacture = $doneeDetailleItemFacture['taxGroup'];
    //         // $idd = $responseRecuperation.ifu;
    //         $nameClient = $decodedDonneFacture['client']['name'];
    //         // dd($prixTotalItemFacture);

    //         // -------------------------------
    //         //  CONFIRMATION DE LA FACTURE 
    //         // -------------------------------

    //         // ACTION pour la confirmation
    //         $actionConfirmation = 'confirm';

    //         // Définissez l'URL de l'API de confirmation de facture
    //         $confirmationUrl = 'https://developper.impots.bj/sygmef-emcf/api/invoice/' . $uid . '/' . $actionConfirmation;

    //         // Configuration de la requête cURL pour la confirmation
    //         $chConfirmation = curl_init($confirmationUrl);
    //         curl_setopt($chConfirmation, CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($chConfirmation, CURLOPT_CUSTOMREQUEST, 'PUT');
    //         curl_setopt($chConfirmation, CURLOPT_HTTPHEADER, [
    //             'Authorization: Bearer ' . $token,
    //             'Content-Length: 0'
    //         ]);
    //         curl_setopt($chConfirmation, CURLOPT_CAINFO, storage_path('certificates/cacert.pem'));

    //         // Exécutez la requête cURL pour la confirmation
    //         $responseConfirmation = curl_exec($chConfirmation);

    //         // Vérifiez les erreurs de cURL pour la confirmation
    //         if (curl_errno($chConfirmation)) {
    //             // echo 'Erreur cURL pour la confirmation : ' . curl_error($chConfirmation);/
    //             return redirect('classes')->with('erreur', 'Erreur curl pour la confirmation');
    //         }

    //         // Fermez la session cURL pour la confirmation
    //         curl_close($chConfirmation);

    //         // Convertissez la réponse JSON en tableau associatif PHP
    //         $decodedResponseConfirmation = json_decode($responseConfirmation, true);
    //         // dd($decodedResponseConfirmation);


    //         $codemecef = $decodedResponseConfirmation['codeMECeFDGI'];

    //         $counters = $decodedResponseConfirmation['counters'];

    //         $nim = $decodedResponseConfirmation['nim'];

    //         $dateTime = $decodedResponseConfirmation['dateTime'];

    //         // Générer le code QR
    //         $qrCodeString = $decodedResponseConfirmation['qrCode'];

    //         $reffactures = $nim . '-' . $counters;

    //         $reffacture = explode('/', $reffactures)[0];

    //         // gestion du code qr sous forme d'image

    //         $fileNameqrcode = $elevyo  . time() . '.png';
    //         $result = Builder::create()
    //             ->writer(new PngWriter())
    //             ->data($qrCodeString)
    //             ->size(100)
    //             // ->margin(10)
    //             ->build();

    //         $qrcodecontent = $result->getString();

    //         $nouveauContrat = new Contrat();
    //         $nouveauContrat->eleve_contrat = $eleveId;
    //         $nouveauContrat->cout_contrat = $montant;
    //         $nouveauContrat->id_usercontrat = $id_usercontratInt;
    //         $nouveauContrat->statut_contrat = 1;
    //         $nouveauContrat->datecreation_contrat = $dateContrat;
    //         $nouveauContrat->dateversion_contrat = $dateContrat;
    //         $nouveauContrat->save();

    //         // Récupérer l'ID du contrat récemment créé
    //         $idContratNouv = $nouveauContrat->id_contrat;

    //         $infoParamContrat = Paramcontrat::first();
    //         $debutAnneeEnCours = $infoParamContrat->anneencours_paramcontrat;
    //         $anneeSuivante = $debutAnneeEnCours + 1;
    //         $anneeScolaireEnCours = $debutAnneeEnCours . '-' . $anneeSuivante;


    //         // enregistrement dans paiementcontrat
    //         $nouveauPaiementcontrat = new Paiementcontrat();
    //         $nouveauPaiementcontrat->soldeavant_paiementcontrat = $montantinteger;
    //         $nouveauPaiementcontrat->montant_paiementcontrat = $montantinteger;
    //         $nouveauPaiementcontrat->soldeapres_paiementcontrat = 0;
    //         $nouveauPaiementcontrat->id_contrat = $idContratNouv;
    //         $nouveauPaiementcontrat->date_paiementcontrat = $dateContrat;
    //         $nouveauPaiementcontrat->id_usercontrat = $id_usercontratInt;
    //         $nouveauPaiementcontrat->mois_paiementcontrat = 13;
    //         $nouveauPaiementcontrat->anne_paiementcontrat = $debutAnneeEnCours;
    //         $nouveauPaiementcontrat->reference_paiementcontrat = $reffacture;
    //         $nouveauPaiementcontrat->statut_paiementcontrat = 1;
    //         $nouveauPaiementcontrat->montanttotal = $montantinteger;
    //         // $nouveauPaiementcontrat->dateversion_contrat = $dateContrat;
    //         $nouveauPaiementcontrat->save();

    //         do {
    //             // Génère un nombre aléatoire entre 10000000 et 99999999
    //             $valeurDynamiqueNumerique = mt_rand(10000000, 99999999);
    //         } while (DB::table('paiementglobalcontrat')->where('reference_paiementcontrat', $valeurDynamiqueNumerique)->exists());

    //         // ENREGISTREMENT DANS LA TABLE INSCRIPTIONCONTRAT
    //         // Parcourir les mois cochés et insérer chaque id de mois dans la table Inscriptioncontrat
    //         foreach ($moisCoches as $id_moiscontrat) {
    //             // $saveMois == 1;
    //             if ($saveMois == 0) {
    //                 Inscriptioncontrat::create([
    //                     // 'id_paiementcontrat ' => $valeurDynamiqueidpaiemnetcontrat, 
    //                     'id_contrat' => $idContratNouv,
    //                     'id_moiscontrat' => $id_moiscontrat,
    //                     'anne_inscription' => $debutAnneeEnCours,

    //                 ]);
    //             } else {
    //                 // 
    //             }
    //         }

    //         // recuperer les nom des mois cochee

    //         // Array des noms des mois
    //         $nomsMoisCoches = [];

    //         // Parcourir les ID des mois cochés et obtenir leur nom correspondant
    //         foreach ($moisCoches as $id_moiscontrat) {
    //             // Ici, vous pouvez récupérer le nom du mois à partir de votre modèle Mois
    //             $mois = Moiscontrat::where('id_moiscontrat', $id_moiscontrat)->first();

    //             // Vérifiez si le mois existe
    //             if ($mois) {
    //                 // Ajouter le nom du mois à l'array
    //                 $nomsMoisCoches[] = $mois->nom_moiscontrat;
    //             }
    //         }

    //         // Convertir le tableau en une chaîne de caractères
    //         $moisConcatenes = implode(',', $nomsMoisCoches);
    //         // dd($moisConcatenes);
    //         // Récupérer la somme des montants de paiement précédents
    //         $soldeavant_paiementcontrat = DB::table('paiementglobalcontrat')
    //             ->where('id_contrat', $idContratNouv)
    //             ->sum('montant_paiementcontrat');


    //         $InfoUtilisateurConnecter =  User::where('id', $id_usercontrat)->first();
    //         $idUserCont =  $InfoUtilisateurConnecter->id;
    //         $idUserContInt = intval($idUserCont);

    //         // dd($soldeavant_paiementcontrat);
    //         // Calculer le solde après le paiement en ajoutant le montant du paiement actuel à la somme des montants précédents
    //         $soldeapres_paiementcontrat = $soldeavant_paiementcontrat + $montantmoiscontrat + $montantinteger;
    //         // dd($soldeapres_paiementcontrat);

    //         // ENREGISTREMENT DANS LA TABLE PAIEMENTGLOBALCONTRAT
    //         $paiementglobalcontrat =  new Paiementglobalcontrat();

    //         $paiementglobalcontrat->soldeavant_paiementcontrat = $soldeavant_paiementcontrat;
    //         $paiementglobalcontrat->montant_paiementcontrat = $montanttotal + $montantinteger;
    //         $paiementglobalcontrat->soldeapres_paiementcontrat = $soldeapres_paiementcontrat;
    //         $paiementglobalcontrat->id_contrat = $idContratNouv;
    //         $paiementglobalcontrat->date_paiementcontrat = $datepaiementcontrat;
    //         $paiementglobalcontrat->id_usercontrat = $idUserContInt;
    //         $paiementglobalcontrat->anne_paiementcontrat = $debutAnneeEnCours;
    //         $paiementglobalcontrat->reference_paiementcontrat = $valeurDynamiqueNumerique;
    //         $paiementglobalcontrat->statut_paiementcontrat = 1;
    //         //     $paiementglobalcontrat->datesuppr_paiementcontrat = null;
    //         //    $paiementglobalcontrat->idsuppr_usercontrat = null;
    //         //    $paiementglobalcontrat->motifsuppr_paiementcontrat = null;
    //         $paiementglobalcontrat->mois_paiementcontrat = $moisConcatenes;

    //         $paiementglobalcontrat->save();

    //         // Récupérer l'id_paiementcontrat de la table paiementglobalcontrat qui correspond a l'id du contrat
    //         $idPaiementContrat = Paiementglobalcontrat::where('id_contrat', $idContratNouv)
    //             ->orderBy('id_paiementcontrat', 'desc')
    //             ->value('id_paiementcontrat');
    //         // dd($idPaiementContrat);                

    //         // ENREGISTREMENT DANS LA TABLE PAIEMENTCONTRAT

    //         // dd($soldeavant_paiementcontrat);
    //         // Créer un objet DateTime à partir de la chaîne de caractères
    //         $datezz = new DateTime($datepaiementcontrat);

    //         // Formater la date sans l'heure
    //         $datezzSansHeure = $datezz->format('Y-m-d');  // Cela donnera "2025-02-18"

    //         // Parcourir les mois cochés et insérer chaque id de mois dans la table Paiementcontrat
    //         foreach ($moisCoches as $id_moiscontrat) {
    //             Paiementcontrat::create([
    //                 // 'id_paiementcontrat ' => $valeurDynamiqueidpaiemnetcontrat, 
    //                 'soldeavant_paiementcontrat' => $soldeavant_paiementcontrat,
    //                 'montant_paiementcontrat' => $montantmoiscontrat,
    //                 'soldeapres_paiementcontrat' => $soldeapres_paiementcontrat,
    //                 'id_contrat' => $idContratNouv,
    //                 'date_paiementcontrat' => $datepaiementcontrat,
    //                 // 'date_paiementcontrat' => $datezzSansHeure,
    //                 'id_usercontrat' => $idUserContInt,
    //                 'mois_paiementcontrat' => $id_moiscontrat,
    //                 'anne_paiementcontrat' => $debutAnneeEnCours,
    //                 'reference_paiementcontrat' => $valeurDynamiqueNumerique,
    //                 'statut_paiementcontrat' => 1,
    //                 // 'datesuppr_paiementcontrat' => $anneeActuelle,
    //                 // 'idsuppr_usercontrat' => $anneeActuelle,
    //                 // 'motifsuppr_paiementcontrat' => $anneeActuelle,
    //                 'id_paiementglobalcontrat' => $idPaiementContrat,
    //                 'montanttotal' => $montanttotal + $montantinteger
    //             ]);
    //         }




    //         // CALCUL DU TOTALHT ET TOTALTVA

    //         $TOTALHT = $montanttotal / 1.18;
    //         $totalHTArrondi = 0;
    //         $TOTALTVA = 0;

    //         // ********************************

    //         // dd($ifuEcoleFacture);
    //         $facturenormalise = new Facturenormalise();
    //         $facturenormalise->id = $reffacture;
    //         $facturenormalise->codemecef = $codemecef;
    //         $facturenormalise->counters = $counters;
    //         $facturenormalise->nim = $nim;
    //         $facturenormalise->dateHeure = $dateTime;
    //         $facturenormalise->ifuEcole = $ifuEcoleFacture;
    //         $facturenormalise->MATRICULE = intval($eleveId);
    //         $facturenormalise->idcontrat = $idContratNouv;
    //         $facturenormalise->moispayes = $moisConcatenes;
    //         $facturenormalise->classe = $classes;
    //         $facturenormalise->nom = $nameClient;
    //         $facturenormalise->itemfacture = $jsonItem;
    //         $facturenormalise->designation = 'Frais cantine pour: inscription et' . $moisConcatenes;
    //         $facturenormalise->montant_total = intval($montanttotal + $montantinteger);
    //         // $facturenormalise->TOTALHT = $totalHTArrondi;
    //         // $facturenormalise->TOTALTVA = $TOTALTVA;
    //         $facturenormalise->montant_par_mois = intval($montantmoiscontrat);
    //         $facturenormalise->datepaiementcontrat = $datepaiementcontrat;
    //         $facturenormalise->qrcode = $qrcodecontent;
    //         $facturenormalise->statut = 1;
    //         $facturenormalise->typefac = 1;
    //         $facturenormalise->montantInscription = intval($montantinteger);

    //         $facturenormalise->save();



    //         $paramse = Params2::first();

    //         $logoUrl = $paramse ? $paramse->logoimage : null;

    //         $NOMETAB = $paramse->NOMETAB;

    //         Session::put('factureconfirm', $decodedResponseConfirmation);
    //         Session::put('fileNameqrcode', $fileNameqrcode);
    //         Session::put('facturedetaille', $facturedetaille);
    //         Session::put('reffacture', $reffacture);
    //         Session::put('classeeleve', $classes);
    //         Session::put('nomcompleteleve', $elevyo);
    //         Session::put('toutmoiscontrat', $toutmoiscontrat);
    //         Session::put('nameItemFacture', $nameItemFacture);
    //         Session::put('prixTotalItemFacture', $prixTotalItemFacture);
    //         Session::put('quantityItemFacture', $quantityItemFacture);
    //         Session::put('taxGroupItemFacture', $taxGroupItemFacture);
    //         Session::put('ifuEcoleFacture', $ifuEcoleFacture);
    //         Session::put('qrCodeString', $qrCodeString);
    //         Session::put('itemFacture', $itemFacture);
    //         Session::put('montanttotal', $montanttotal + $montantinteger);
    //         Session::put('totalHTArrondi', $totalHTArrondi);
    //         Session::put('TOTALTVA', $TOTALTVA);
    //         Session::put('montantmoiscontrat', $montantmoiscontrat);
    //         Session::put('qrcodecontent', $qrcodecontent);
    //         Session::put('NOMETAB', $NOMETAB);
    //         Session::put('nim', $nim);
    //         Session::put('datepaiementcontrat', $datepaiementcontrat);
    //         Session::put('dateTime', $dateTime);
    //         // Session::put('nometab', $nometab);
    //         // Session::put('villeetab', $villeetab);




    //         return view('pages.Etats.pdffacture', [
    //             'factureconfirm' => $decodedResponseConfirmation,
    //             'fileNameqrcode' => $fileNameqrcode,
    //             'facturedetaille' => $facturedetaille,
    //             'reffacture' => $reffacture,
    //             'ifuEcoleFacture' => $ifuEcoleFacture,
    //             'nameItemFacture' => $nameItemFacture,
    //             'prixTotalItemFacture' => $prixTotalItemFacture,
    //             'quantityItemFacture' => $quantityItemFacture,
    //             'taxGroupItemFacture' => $taxGroupItemFacture,
    //             'classeeleve' => $classes,
    //             'nomcompleteleve' => $elevyo,
    //             'toutmoiscontrat' => $toutmoiscontrat,
    //             'qrCodeString' => $qrCodeString,
    //             'logoUrl' => $logoUrl,
    //             'itemFacture' => $itemFacture,
    //             'montanttotal' => $montanttotal + $montantinteger,
    //             // 'montantinscription' => $montantinteger,
    //             'qrcodecontent' => $qrcodecontent,
    //             'NOMETAB' => $NOMETAB,
    //             'nim' => $nim,
    //             'datepaiementcontrat' => $datepaiementcontrat,
    //             'dateTime' => $dateTime,
    //             'totalHTArrondi' => $totalHTArrondi,
    //             'TOTALTVA' => $TOTALTVA,
    //             // 'villeetab' => $villeetab,
    //             // 'qrCodeImage' => $qrCodeImage,

    //         ]);
    //     }
    // }
}

