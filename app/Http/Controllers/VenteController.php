<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\Produit;
use App\Models\Vente;
use App\Models\Exercice;
use App\Models\DetailVente;
use App\Models\FactureNormalisee;
use App\Models\ModePaiement;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use DateTime;
use Carbon\Carbon;

class VenteController extends Controller
{

       private function genererNumeroVente(): string
    {
        $annee = Carbon::now()->format('Y');

        // Récupérer la dernière vente de l'année
        $lastVente = Vente::where('reference', 'like', "V-$annee-%")
            ->orderBy('reference', 'desc')
            ->first();

        if ($lastVente) {
            // Extraire le compteur
            $lastNumber = intval(substr($lastVente->reference, -9));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('V-%s-%09d', $annee, $nextNumber);
    }


    public function vente() {
        $allClients = Client::get();
        $allproduits = Produit::get();
        $numVente = $this->genererNumeroVente();
        $exerciceActif = Exercice::where('statutExercice', 1)->firstOrFail();
        $allVente = Vente::where('statutVente', 1)
            ->where('idExercice', $exerciceActif->idExercice)
            ->with('client', 'detailVente', 'factureNormalise')
            ->get();
        // $allVente = Vente::where('statutVente', 1)->with('client', 'detailVente', 'factureNormalise')->get();
        $modes = ModePaiement::get();
        
        // Récupérer le régime de l'entreprise connectée
        $user = auth()->user();
        $entreprise = $user->entreprise;
        $regimeEntreprise = $entreprise ? $entreprise->regime : 'normal';
        
        return view('pages.Facturation.ventes', compact('allClients', 'allproduits', 'allVente', 'modes', 'regimeEntreprise', 'numVente'));
    }

    // public function facturation(Request $request)
    // {
    //     $type = $request->query('type', 'all');

    //     $query = FactureNormalisee::where('statut', 1)
    //         ->with('vente', 'commandeAchat')
    //         ->orderBy('date', 'desc');

    //     if ($type === 'FV') {
    //         // Factures de vente
    //         $query->where('counter', 'like', '% FV');
    //     } elseif ($type === 'FA') {
    //         // Factures d'avoir
    //         $query->where('counter', 'like', '% FA');
    //     }

    //     $allFactures = $query->get();

    //     return view('pages.Facturation.facturation', compact('allFactures', 'type'));
    // }


    public function facturation(Request $request)
    {
        $type = $request->query('type', 'all');

        $exerciceActif = Exercice::where('statutExercice', 1)->first();

        if (!$exerciceActif) {
            abort(404, 'Aucun exercice actif');
        }

        $query = FactureNormalisee::where('statut', 1)
            ->with(['vente', 'commandeAchat'])
            ->where(function ($q) use ($exerciceActif) {
                $q->whereHas('vente', function ($v) use ($exerciceActif) {
                    $v->where('idExercice', $exerciceActif->idExercice);
                })
                ->orWhereHas('commandeAchat', function ($c) use ($exerciceActif) {
                    $c->where('idExercice', $exerciceActif->idExercice);
                });
            })
            ->orderBy('date', 'desc');

        if ($type === 'FV') {
            $query->where('counter', 'like', '%FV');
        } elseif ($type === 'FA') {
            $query->where('counter', 'like', '%FA');
        }

        $allFactures = $query->get();

        return view('pages.Facturation.facturation', compact('allFactures', 'type'));
    }



    


    public function storevente (Request $request) {
        // dd('lolololo');

        // donnees de la vue
        $IFUClient = $request->input('IFUClient');
        $nomClient = $request->input('nomClient');
        $telClient = $request->input('telClient');
        $reference = $request->input('reference');
        $idModPaie = $request->input('idModPaie');
        $dateOperation = $request->input('dateOperation');
        $description = $request->input('description');
        $totalHT = $request->input('totalHT');
        $totalTTC = $request->input('totalTTC');
        $lignes = $request->input('lignes');
        $libellModepaie = $request->input('libelleModePaie');
        // $reference = $request->input('reference');



        // dd($libellModepaie);
        // donne de l'entreprise vendeur
        $user = auth()->user();
        $userId = $user->idU;
        $entrepriseId = $user->idE;
        $entreprise = $user->entreprise;
        $ifuEntreprise = $entreprise->IFU;
        $tokenEntreprise = $entreprise->token;
        $regimeEntreprise = $entreprise->regime;
        $nomEntreprise = $entreprise->nom;
        $telEntreprise = $entreprise->telephone;
        $adresseEntreprise = $entreprise->adresse;
        $mailEntreprise = $entreprise->mail;



        foreach ($request->lignes as $ligne) {

            $produit = Produit::with('stocke')->find($ligne['idP']);

            if (!$produit) {
                return back()->withErrors("Produit introuvable");
            }

            $stockDisponible = $produit->stocke->qteStocke ?? 0;

            if ($ligne['qte'] > $stockDisponible) {
                return back()->withErrors(
                    "Stock insuffisant pour {$produit->libelle}. Disponible : {$stockDisponible}"
                );
            }
        }

        // dd($ifuEntreprise, $tokenEntreprise);
        // preparation des items de la facture
        $items = []; // Initialiser un tableau vide pour les éléments

        
        foreach ($lignes as $article ) {
            $items[] = [
                // 'name' => 'Frais cantine pour : ' . $mois, 
                'name' => $article['libelle'], 
                // 'price' => intval($montantmoiscontrat),
                'price' => intval($article['prixU']),
                'quantity' => intval($article['qte']),
                'taxGroup' => $article['taxe'],
            ];
        }


             // Préparez les données JSON pour l'API
        $jsonData = json_encode([
            "ifu" => $ifuEntreprise, // ici on doit rendre la valeur de l'ifu dynamique
            // "aib" => "A",
            "type" => 'FV',
            "items" => $items,

            "client" => [
                "ifu" => $IFUClient ?? '',
                "name" =>  $nomClient ?? '',
                "contact" => $telClient ?? '',
                // "address"=> "string"
            ],
            "operator" => [
                "name" => "CRYSTAL SERVICE INFO (TONY ABAMAN FIRMIN)"
            ],
            "payment" => [
                [
                    "name" => $libellModepaie ?? "ESPECES",  // mettre $ModPaie et s'assurer qu'il correspond a ('ESPECES, CHEQUE, AUTRE)
                    "amount" => intval($totalTTC)
                ]
            ],
        ]);

        $apiUrl = 'https://developper.impots.bj/sygmef-emcf/api/invoice';

        $token = $tokenEntreprise;

        // dd($jsonData);


        // Effectuez la requête POST à l'API
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_CAINFO, storage_path('certificates/cacert.pem'));

        // Exécutez la requête cURL et récupérez la réponse
        $response = curl_exec($ch);

        // Vérifiez les erreurs de cURL
        if (curl_errno($ch)) {
            // echo 'Erreur cURL : ' . curl_error($ch);
            return back()->with('erreur', 'Erreur curl , mauvaise connexion a l\'API');
        }

        // Fermez la session cURL
        curl_close($ch);

        // Affichez la réponse de l'API
        $decodedResponse = json_decode($response, true);
        // dd($decodedResponse);


        // Vérifiez si l'UID est présent dans la réponse
        if (isset($decodedResponse['uid'])) {
            // L'UID de la demande
            $uid = $decodedResponse['uid'];
            // $taxb = 0.18;

            // Affichez l'UID
            // echo "L'UID de la demande est : $uid";


            // -------------------------------
            //  RECUPERATION DE LA FACTURE PAR SON UID
            // -------------------------------

            // Définissez l'URL de l'API de confirmation de facture
            $recuperationUrl = 'https://developper.impots.bj/sygmef-emcf/api/invoice/' . $uid;

            // Configuration de la requête cURL pour la confirmation
            $chRecuperation = curl_init($recuperationUrl);
            curl_setopt($chRecuperation, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($chRecuperation, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($chRecuperation, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Length: 0'
            ]);
            curl_setopt($chRecuperation, CURLOPT_CAINFO, storage_path('certificates/cacert.pem'));

            // Exécutez la requête cURL pour la confirmation
            $responseRecuperation = curl_exec($chRecuperation);
            // dd($responseRecuperation);
            // Vérifiez les erreurs de cURL pour la confirmation


            // Fermez la session cURL pour la confirmation
            curl_close($chRecuperation);

            // Convertissez la réponse JSON en tableau associatif PHP
            $decodedDonneFacture = json_decode($responseRecuperation, true);
            // dd($decodedDonneFacture);

            $facturedetaille = json_decode($jsonData, true);
            $ifuEcoleFacture = $decodedDonneFacture['ifu'];
            $itemFacture = $decodedDonneFacture['items'];
            $jsonItem = json_encode($itemFacture);
            $doneeDetailleItemFacture = $itemFacture['0'];
            $nameItemFacture = $doneeDetailleItemFacture['name'];
            $prixTotalItemFacture = $doneeDetailleItemFacture['price'];
            $quantityItemFacture = $doneeDetailleItemFacture['quantity'];
            $taxGroupItemFacture = $doneeDetailleItemFacture['taxGroup'];
            // $idd = $responseRecuperation.ifu;
            $nameClient = $decodedDonneFacture['client']['name'];
            // dd($prixTotalItemFacture);

            // -------------------------------
    //         //  CONFIRMATION DE LA FACTURE 
    //         // -------------------------------

    //         // ACTION pour la confirmation
            $actionConfirmation = 'confirm';

            // Définissez l'URL de l'API de confirmation de facture
            $confirmationUrl = 'https://developper.impots.bj/sygmef-emcf/api/invoice/' . $uid . '/' . $actionConfirmation;

            // Configuration de la requête cURL pour la confirmation
            $chConfirmation = curl_init($confirmationUrl);
            curl_setopt($chConfirmation, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($chConfirmation, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($chConfirmation, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Length: 0'
            ]);
            curl_setopt($chConfirmation, CURLOPT_CAINFO, storage_path('certificates/cacert.pem'));

            // Exécutez la requête cURL pour la confirmation
            $responseConfirmation = curl_exec($chConfirmation);

            // Vérifiez les erreurs de cURL pour la confirmation
            if (curl_errno($chConfirmation)) {
                // echo 'Erreur cURL pour la confirmation : ' . curl_error($chConfirmation);/
                return redirect('classes')->with('erreur', 'Erreur curl pour la confirmation');
            }

            // Fermez la session cURL pour la confirmation
            curl_close($chConfirmation);

            // Convertissez la réponse JSON en tableau associatif PHP
            $decodedResponseConfirmation = json_decode($responseConfirmation, true);
            // dd($decodedResponseConfirmation);


            $codemecef = $decodedResponseConfirmation['codeMECeFDGI'];

            $counters = $decodedResponseConfirmation['counters'];

            $nim = $decodedResponseConfirmation['nim'];

            $dateTime = $decodedResponseConfirmation['dateTime'];

            // Générer le code QR
            $qrCodeString = $decodedResponseConfirmation['qrCode'];

            $reffactures = $nim . '-' . $counters;

            $reffacture = explode('/', $reffactures)[0];

            // gestion du code qr sous forme d'image

            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($qrCodeString)
                ->size(100)
                // ->margin(10)
                ->build();

            $qrcodecontent = $result->getString();

            
            // Enregistrer la vente
            $idExercice = Exercice::where('statutExercice', 1)
            ->firstOrFail()
            ->idExercice;

            // Créer un objet DateTime à partir de la chaîne de caractères
            $datezz = new DateTime($dateOperation);

            // Formater la date sans l'heure
            $datezzSansHeure = $datezz->format('Y-m-d');  // Cela donnera "2025-02-18"
            
            if ($regimeEntreprise == 'TPS') {
                $TotalTVA = 0;
            } else {
                $TotalTVA = -(ceil($totalTTC / 1.18) - $totalTTC);
            }
            


            $referenceVente = $reference;
            // dd($datezzSansHeure);
            // Debut de la transaction
            DB::transaction(function () use (
                $dateOperation,
                $totalTTC,
                $IFUClient,
                $nomClient,
                $telClient,
                $userId,
                $entrepriseId,
                $idExercice,
                $idModPaie,
                $lignes,
                $reffacture,
                $codemecef,
                $counters,
                $nim,
                $referenceVente,
                $dateTime,
                $jsonItem,
                $taxGroupItemFacture,
                $qrcodecontent,
                $regimeEntreprise,
                $TotalTVA ,
            ) {

                // =========================
                // ENREGISTREMENT VENTE
                // =========================
                $vente = new Vente();
                $vente->dateOperation = $dateOperation;
                $vente->montantTotal = intval($totalTTC);
                $vente->reference = $referenceVente;
                $vente->statutVente = 1;
                $vente->IFUClient = $IFUClient;
                $vente->nomClient = $nomClient;
                $vente->telClient = $telClient;
                $vente->idU = intval($userId);
                $vente->idE = intval($entrepriseId);
                $vente->idExercice = intval($idExercice);
                $vente->idModPaie = intval($idModPaie);
                $vente->save();

                // =========================
                // DETAILS DE VENTE
                // =========================
                foreach ($lignes as $article) {
                    DetailVente::create([
                        'qte' => intval($article['qte']),
                        'prixUnit' => intval($article['prixU']),
                        'montantHT' => intval($article['montantht']),
                        'montantTTC' => intval($article['montantttc']),
                        'idPro' => intval($article['idP']),
                        'idV' => $vente->idV,
                    ]);
                }

                // =========================
                // MISE À JOUR DU STOCK
                // =========================
                foreach ($lignes as $ligne) {
                    $produit = Produit::with('stocke')->findOrFail($ligne['idP']);
                    $produit->stocke->decrement('qteStocke', $ligne['qte']);
                }

                // =========================
                // FACTURE NORMALISÉE
                // =========================
                $facturenormalise = new Facturenormalisee();
                $facturenormalise->reffacture = $reffacture;
                $facturenormalise->CODEMECEF = $codemecef;
                $facturenormalise->counter = $counters;
                $facturenormalise->nim = $nim;
                $facturenormalise->date = $dateTime;
                $facturenormalise->itemFacture = $jsonItem;
                $facturenormalise->groupeTaxation = $taxGroupItemFacture;
                $facturenormalise->montantTotal = intval($totalTTC);
                $facturenormalise->montantTotalTTC = intval($totalTTC);
                $facturenormalise->TotalTVA = $TotalTVA;
                $facturenormalise->qrcode = $qrcodecontent;
                $facturenormalise->regime = $regimeEntreprise;
                $facturenormalise->statut = 1;
                $facturenormalise->idV = $vente->idV;
                $facturenormalise->save();
            });


            // dd('Oki');

            // $paramse = Params2::first();

            // $logoUrl = $paramse ? $paramse->logoimage : null;

            // $NOMETAB = $paramse->NOMETAB;


                return view('pages.Facturation.facturevente', [
                'factureconfirm' => $decodedResponseConfirmation,
                // 'fileNameqrcode' => $fileNameqrcode,
                'facturedetaille' => $jsonItem,
                'reffacture' => $referenceVente,
                'nomcompletClient' => $nomClient,
                'telClient' => $telClient,
                'IFUClient' => $IFUClient,
                'qrCodeString' => $qrCodeString,
                // 'logoUrl' => $logoUrl,
                'itemFacture' => $itemFacture,
                'regime' => $regimeEntreprise,
                'montanttotal' => $totalTTC,
                'TotalTVA' => $TotalTVA,
                'qrcodecontent' => $qrcodecontent,
                'nim' => $nim,
                'dateTime' => $dateTime,
                // 'vente' => $vente,
                'nomEntreprise' => $nomEntreprise,
                'adresseEntreprise' => $adresseEntreprise,
                'telEntreprise' => $telEntreprise,
                'mailEntreprise' => $mailEntreprise,
                'IFUEntreprise' => $ifuEntreprise,
                'libellModepaie' => $libellModepaie,
                // 'detailvante' => $vente,
            ]);
        }

    }

    public function duplicatafacture($id) {

        $facture = Facturenormalisee::where('idFacture', $id)
            ->with('vente')
            ->first();

            $itemDecode = json_decode($facture->itemFacture, true);

            $infoEntreprise = $facture->vente->entreprise;


            $counter = $facture->counter;

            $type = str_ends_with($counter, 'FA') ? 'FA' : 'FV';

            return view('pages.Facturation.duplicatafacturevente', [
                'reffacture' => $facture->vente->reference,
                'nomcompletClient' => $facture->vente->nomClient,
                'telClient' => $facture->vente->telClient,
                'IFUClient' => $facture->vente->IFUClient,
                // 'qrCodeString' => $qrCodeString,
                // 'logoUrl' => $logoUrl,
                'itemFacture' => $itemDecode,
                'type' => $type,
                'montanttotal' => $facture->montantTotal,
                'regime' => $facture->regime,
                'CODEMECEFfacOriginale' => $facture->CODEMECEFfacOriginale,
                'TotalTVA' => $facture->TotalTVA,
                'qrcodecontent' => $facture->qrcode,
                'codeMECeFDGI' => $facture->CODEMECEF,
                'counters' => $facture->counter,
                'nim' => $facture->nim,
                'dateTime' => $facture->date,
                // 'vente' => $vente,
                'nomEntreprise' => $infoEntreprise->nom,
                'adresseEntreprise' => $infoEntreprise->adresse,
                'telEntreprise' => $infoEntreprise->telephone,
                'mailEntreprise' => $infoEntreprise->mail,
                'IFUEntreprise' => $infoEntreprise->IFU,
                'libellModepaie' => $facture->vente->modePaiement->libelle,
                // 'detailvante' => $vente,
            ]);

    }
 
    
    public function updateVente(Request $request, $idV) {
        $vente = Vente::findOrFail($idV);
        
        $request->validate([
            'reference' => 'required|string|unique:vente,reference,'.$idV.',idV',
            'dateOperation' => 'required|date',
            'IFUClient' => 'nullable|string',
            'nomClient' => 'nullable|string',
            'telClient' => 'nullable|string',
            'idModPaie' => 'nullable|exists:mode_paiement,idModPaie',
            'lignes' => 'required|array|min:1',
            'lignes.*.idP' => 'required|exists:produit,idPro',
            'lignes.*.qte' => 'required|integer|min:1',
            'lignes.*.montantttc' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            $vente->update([
                'reference' => $request->reference,
                'dateOperation' => $request->dateOperation,
                'IFUClient' => $request->IFUClient,
                'nomClient' => $request->nomClient,
                'telClient' => $request->telClient,
                'idModPaie' => $request->idModPaie,
                'montantTotal' => 0
            ]);

            DetailVente::where('idV', $idV)->delete();
            
            $montantTotal = 0;
            foreach ($request->lignes as $ligne) {
                $montantTotal += $ligne['montantttc'];
                
                DetailVente::create([
                    'idV' => $vente->idV,
                    'idP' => $ligne['idP'],
                    'quantite' => $ligne['qte'],
                    'prixUnitaire' => $ligne['montantttc'] / $ligne['qte'],
                    'montantTotal' => $ligne['montantttc']
                ]);
            }

            $vente->update(['montantTotal' => $montantTotal]);
            
            DB::commit();
            return redirect()->route('ventes')->with('status', 'Vente modifiée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('erreur', 'Erreur lors de la modification de la vente')->withInput();
        }
    }

    public function deletevente(Request $request, $idFacture) {


        $codemecefEntrer = $request->input('codemecef');

            $factureoriginale = FactureNormalisee::where('idFacture', $idFacture)->with('vente')->first();
            $codemecefOriginale = $factureoriginale->CODEMECEF;

        if ($codemecefEntrer == $codemecefOriginale) {

            $infoVente = $factureoriginale->vente;
            $infoEntreprise = $factureoriginale->vente->entreprise;

            $tokenentreprise = $infoEntreprise->token;
            $ifuentreprise = $infoEntreprise->IFU;
            $montanttotal = $factureoriginale->montantTotal;
            $TOTALTVA = $factureoriginale->TotalTVA;
            $nomCompletClient = $infoVente->nomClient;
            $IfuClient = $infoVente->IFUClient;
            $telClient = $infoVente->telClient;
            $datepaiement = $factureoriginale->date;
            $idV = $factureoriginale->idV;

            $item = $factureoriginale->itemFacture; // Initialiser un tableau vide pour les éléments
            $items= json_decode($item, true);

            // dd($items);

            // Préparez les données JSON pour l'API
            $jsonData = json_encode([
                "ifu" => $ifuentreprise, // ici on doit rendre la valeur de l'ifu dynamique
                // "aib" => "A",
                "type" => 'FA',
                "items" => $items,
                "client" => [
                    "ifu" => $IfuClient ?? '',
                    "name" =>  $nomCompletClient ?? '',
                    "contact" => $telClient ?? '',
                    // "address"=> "string"
                ],
                "operator" => [
                    "name" => " CRYSTAL SERVICE INFO (TONY ABAMAN FIRMIN)"
                ],
                "payment" => [
                    [
                        "name" => $infoVente->modePaiement->libelle ?? 'ESPECES',
                        "amount" => intval($montanttotal),
                    ]
                ],
                "reference" => $codemecefOriginale,
            ]);
            // $jsonDataliste = json_encode($jsonData, JSON_FORCE_OBJECT);


            //  dd($jsonData);

            // Définissez l'URL de l'API de facturation
            $apiUrl = 'https://developper.impots.bj/sygmef-emcf/api/invoice';

            // Définissez le jeton d'authentification
            // $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IjAyMDIzODU5MTExMzh8VFMwMTAxMTQ3MiIsInJvbGUiOiJUYXhwYXllciIsIm5iZiI6MTcyNDI1NzQyMywiZXhwIjoxNzM3NDE0MDAwLCJpYXQiOjE3MjQyNTc0MjMsImlzcyI6ImltcG90cy5iaiIsImF1ZCI6ImltcG90cy5iaiJ9.sRcSeEbIuQNSgFebRRaxW4zPLCqlF6PQXc90e2xfHCs';
            $token = $tokenentreprise;

            // Effectuez la requête POST à l'API
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ));
            curl_setopt($ch, CURLOPT_CAINFO, storage_path('certificates/cacert.pem'));

            // Exécutez la requête cURL et récupérez la réponse
            $response = curl_exec($ch);
            // dd($response);

            $decodedResponse = json_decode($response, true);

            // Vérifiez si l'UID est présent dans la réponse
            if (isset($decodedResponse['uid'])) {
                // L'UID de la demande
                $uid = $decodedResponse['uid'];
                // $taxb = 0.18;

                // -------------------------------
                //  RECUPERATION DE LA FACTURE PAR SON UID
                // -------------------------------

                // Définissez l'URL de l'API de confirmation de facture
                $recuperationUrl = 'https://developper.impots.bj/sygmef-emcf/api/invoice/' . $uid;

                // Configuration de la requête cURL pour la confirmation
                $chRecuperation = curl_init($recuperationUrl);
                curl_setopt($chRecuperation, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($chRecuperation, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($chRecuperation, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $token,
                    'Content-Length: 0'
                ]);
                curl_setopt($chRecuperation, CURLOPT_CAINFO, storage_path('certificates/cacert.pem'));

                // Exécutez la requête cURL pour la confirmation
                $responseRecuperation = curl_exec($chRecuperation);
                // dd($responseRecuperation);
                // Vérifiez les erreurs de cURL pour la confirmation


                // Fermez la session cURL pour la confirmation
                curl_close($chRecuperation);

                // Convertissez la réponse JSON en tableau associatif PHP
                $decodedDonneFacture = json_decode($responseRecuperation, true);

                // $facturedetaille = json_decode($jsonData, true);
                $ifuEcoleFacture = $decodedDonneFacture['ifu'];
                // dd($ifuEcoleFacture);
                $itemFacture = $decodedDonneFacture['items'];
                $jsonItems = json_encode($itemFacture);
                $doneeDetailleItemFacture = $itemFacture['0'];
                $nameItemFacture = $doneeDetailleItemFacture['name'];
                $prixTotalItemFacture = $doneeDetailleItemFacture['price'];
                $quantityItemFacture = $doneeDetailleItemFacture['quantity'];
                $taxGroupItemFacture = $doneeDetailleItemFacture['taxGroup'];
                // $idd = $responseRecuperation.ifu;
                $nameClient = $decodedDonneFacture['client']['name'];
                // dd($itemFacture);

                // -------------------------------
                //  CONFIRMATION DE LA FACTURE 
                // -------------------------------

                // ACTION pour la confirmation
                $actionConfirmation = 'confirm';

                // Définissez l'URL de l'API de confirmation de facture
                $confirmationUrl = 'https://developper.impots.bj/sygmef-emcf/api/invoice/' . $uid . '/' . $actionConfirmation;

                // Configuration de la requête cURL pour la confirmation
                $chConfirmation = curl_init($confirmationUrl);
                curl_setopt($chConfirmation, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($chConfirmation, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($chConfirmation, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $token,
                    'Content-Length: 0'
                ]);
                curl_setopt($chConfirmation, CURLOPT_CAINFO, storage_path('certificates/cacert.pem'));

                // Exécutez la requête cURL pour la confirmation
                $responseConfirmation = curl_exec($chConfirmation);


                // Fermez la session cURL pour la confirmation
                curl_close($chConfirmation);

                // Convertissez la réponse JSON en tableau associatif PHP
                $decodedResponseConfirmation = json_decode($responseConfirmation, true);
                // dd($decodedResponseConfirmation);


                $codemecefavoir = $decodedResponseConfirmation['codeMECeFDGI'];

                $counters = $decodedResponseConfirmation['counters'];

                $nim = $decodedResponseConfirmation['nim'];

                $dateTime = $decodedResponseConfirmation['dateTime'];

                // Générer le code QR
                $qrCodeString = $decodedResponseConfirmation['qrCode'];

                $reffactures = $nim . '-' . $counters;

                $reffacture = explode('/', $reffactures)[0];



                // dd($reffacture);

                // gestion du code qr sous forme d'image

                // $fileNameqrcode = $nomcompleteleve . time() . '.png';
                $result = Builder::create()
                    ->writer(new PngWriter())
                    ->data($qrCodeString)
                    ->size(100)
                    // ->margin(10)
                    ->build();

                $qrcodecontent = $result->getString();

                //   dd($jsonItems);

                // ENREGISTREMENT DE LA FACTURE
                $facturenormalise = new Facturenormalisee();
                $facturenormalise->reffacture = $reffacture;
                $facturenormalise->CODEMECEF = $codemecefavoir;
                $facturenormalise->codemeceffacoriginale = $codemecefOriginale;
                $facturenormalise->counter = $counters;
                $facturenormalise->nim = $nim;
                $facturenormalise->date = $dateTime;
                $facturenormalise->itemFacture = $jsonItems;
                $facturenormalise->groupeTaxation = $factureoriginale->groupeTaxation;
                $facturenormalise->montantTotal = $factureoriginale->montantTotal;
                $facturenormalise->montantTotalTTC = intval($factureoriginale->montantTotalTTC);
                $facturenormalise->TotalTVA = intval($factureoriginale->TotalTVA);
                $facturenormalise->qrcode = $qrcodecontent;
                $facturenormalise->statut = 1;
                $facturenormalise->regime = $factureoriginale->regime;
                $facturenormalise->idV = $factureoriginale->idV;
                $facturenormalise->save();

                DB::table('facture_normalisees')
                    ->where('CODEMECEF', $codemecefOriginale)
                    ->update(['statut' => 0]);


            try {
                DB::beginTransaction();

                // Récupérer les lignes de la vente originale
                $details = \App\Models\DetailVente::where('idV', $idV)->get();

                foreach ($details as $detail) {
                    // Verrouiller la ligne de stock pour éviter course-condition
                    $stock = \App\Models\Stocke::where('idPro', $detail->idPro)->lockForUpdate()->first();

                    if ($stock) {
                        // Incrémente la quantité en stock
                        $stock->increment('qteStocke', intval($detail->qte));
                    } else {
                        // Si aucune ligne stock n'existe, tu peux en créer une (ou loguer l'erreur)
                        return back()->with('erreur', "Erreur lors de la restitution du stock : ");
                    }
                }

                // Marquer la vente comme annulée / A VOIR selon ta logique (tu l'as déjà fait)
                Vente::where('idV', $idV)->update(['statutVente' => 0]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                // log l'erreur puis retourne avec message
                \Log::error('Erreur restauration stock pour vente '.$idV.': '.$e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return back()->with('erreur', "Erreur lors de la restitution du stock : " . $e->getMessage());
            }

                // Vente::where('idV', $idV)
                //     ->update(['statutVente' => 0]);


                // return back()->with('status', "Facture d'avoir generer avec succes");

                return view('pages.Facturation.factureavoir', [
                'factureconfirm' => $decodedResponseConfirmation,
                // 'fileNameqrcode' => $fileNameqrcode,
                'facturedetaille' => $jsonItems,
                'reffacture' => $infoVente->reference,
                'nomcompletClient' => $nameClient,
                'telClient' => $telClient,
                'IFUClient' => $IfuClient,
                // 'qrCodeString' => $qrCodeString,
                // 'logoUrl' => $logoUrl,
                'itemFacture' => $itemFacture,
                'regime' => $factureoriginale->regime,
                'montanttotal' => $factureoriginale->montantTotalTTC,
                'TotalTVA' => $factureoriginale->TotalTVA,
                'qrcodecontent' => $qrcodecontent,
                'nim' => $nim,
                'codemecefOriginale' => $codemecefOriginale,
                'dateTime' => $dateTime,
                // 'vente' => $vente,
                'nomEntreprise' => $infoEntreprise->nom,
                'adresseEntreprise' => $infoEntreprise->adresse,
                'telEntreprise' => $infoEntreprise->telephone,
                'mailEntreprise' => $infoEntreprise->mail,
                'IFUEntreprise' => $infoEntreprise->IFU,
                'libellModepaie' => $infoVente->modePaiement->libelle,
                // 'detailvante' => $vente,
            ]);

        }
        } else {
            // dd('codemecef incorrect');
            return back()->with('erreur', "Le codemecef entrer ne correspond pas a celui de la facture originale.");
        }

        // try {
        //     $vente = Vente::findOrFail($idV);
        //     DetailVente::where('idV', $idV)->delete();
        //     $vente->delete();
        //     return redirect()->route('ventes')->with('status', 'Vente supprimée avec succès');
        // } catch (\Exception $e) {
        //     return redirect()->route('ventes')->with('erreur', 'Erreur lors de la suppression de la vente');
        // }
    }

    public function deleteLigneVente($id) {
        try {
            $ligne = DetailVente::findOrFail($id);
            $ligne->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    public function getNouvelleReference() {
        $derniereVente = Vente::orderBy('idV', 'desc')->first();
        $numero = $derniereVente ? intval(substr($derniereVente->reference, 4)) + 1 : 1;
        return response()->json(['reference' => 'VTE-' . str_pad($numero, 4, '0', STR_PAD_LEFT)]);
    }

    public function getProduitInfo($id) {
        $produit = Produit::find($id);
        if ($produit) {
            return response()->json([
                'prix' => $produit->prix,
                'libelle' => $produit->libelle,
                'taxe' => $produit->familleProduit->groupe,
                'stock' => $produit->stocke->qteStocke ?? 0,
            ]);
        }
        return response()->json([
            'prix' => 0,
            'libelle' => '',
            'taxe' => 0,
        ]);
    }





}
