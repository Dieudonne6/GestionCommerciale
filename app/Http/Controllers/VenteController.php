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
        $allVente = Vente::with('client', 'detailVente')->get();
        $modes = ModePaiement::get();
        return view('pages.Facturation.ventes', compact('allClients', 'allproduits', 'allVente', 'modes'));
    }

    public function facturation() {
        $allFactures = FactureNormalisee::with('vente.client', 'commandeAchat')->get();
        return view('pages.Facturation.facturation', compact('allFactures'));
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



        dd($lignes);
        // donne de l'entreprise vendeur
        $user = auth()->user();
        $userId = $user->idU;
        $entrepriseId = $user->idE;
        $entreprise = $user->entreprise;
        $ifuEntreprise = $entreprise->IFU;
        $tokenEntreprise = $entreprise->token;
        $regimeEntreprise = $entreprise->regime;

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
                "name" => " CRYSTAL SERVICE INFO (TONY ABAMAN FIRMIN)"
            ],
            "payment" => [
                [
                    "name" => "ESPECES",  // mettre $ModPaie et s'assurer qu'il correspond a ('ESPECES, CHEQUE, AUTRE)
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

            // $fileNameqrcode = $elevyo  . time() . '.png';
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($qrCodeString)
                ->size(100)
                // ->margin(10)
                ->build();

            $qrcodecontent = $result->getString();





            // recuperer les nom des mois cochee

            // Enregistrer la vente


            $idExercice = Exercice::where('statutExercice', 1)
                ->firstOrFail()
                ->idExercice;

            $vente = new Vente();
            $vente->dateOperation = $dateOperation;
            $vente->montantTotal = $totalTTC;
            $vente->reference = $this->genererNumeroVente();
            $vente->statutVente = 1;
            $vente->IFUClient = $IFUClient;
            $vente->nomClient = $nomClient;
            $vente->telClient = $telClient;
            $vente->idU = $userId;
            $vente->idE = $entrepriseId;
            $vente->idExercice = $idExercice;
            $vente->idModPaie = $idModPaie;
            $vente->save();


            // Enregistrer les details de la vente 

            foreach ($lignes as $article ) {
                DetailVente::create([
                    'qte' => intval($article['qte']), 
                    'prixUnit' => intval($article['prixU']), 
                    'montantHT' => intval($article['montantht']), 
                    'montantTTC' => intval($article['montantttc']), 
                    'idPro' => intval($article['idP']), 
                    'idV' => $vente->idV, 
                ]); 
            }

           
           
            // Créer un objet DateTime à partir de la chaîne de caractères
            $datezz = new DateTime($dateOperation);

            // Formater la date sans l'heure
            $datezzSansHeure = $datezz->format('Y-m-d');  // Cela donnera "2025-02-18"






            // CALCUL DU TOTALHT ET TOTALTVA

            // $TOTALHT = $montanttotal / 1.18;
            // $totalHTArrondi = 0;
            // $TOTALTVA = 0;

            // ********************************

            // dd($ifuEcoleFacture);
            $facturenormalise = new Facturenormalisee();
            $facturenormalise->reffacture = $reffacture;
            $facturenormalise->CODEMECEF = $codemecef;
            $facturenormalise->counter = $counters;
            $facturenormalise->nim = $nim;
            $facturenormalise->date = $dateTime;
            // $facturenormalise->nom = $nameClient;
            $facturenormalise->itemFacture = $jsonItem;
            $facturenormalise->groupeTaxation = $taxGroupItemFacture;
            // $facturenormalise->designation = 'Frais cantine pour: inscription et' . $moisConcatenes;
            $facturenormalise->montantTotal = $totalTTC;
            $facturenormalise->montantTotalTTC = $totalTTC;
            // $facturenormalise->TOTALHT = $totalHTArrondi;
            // $facturenormalise->TOTALTVA = $TOTALTVA;
            $facturenormalise->qrcode = $qrcodecontent;
            $facturenormalise->statut = 1;
            $facturenormalise->idV = $vente->idV;
            // $facturenormalise->typefac = 1;

            $facturenormalise->save();


            dd('Oki');

            // $paramse = Params2::first();

            // $logoUrl = $paramse ? $paramse->logoimage : null;

            // $NOMETAB = $paramse->NOMETAB;


                return view('pages.Facturation.pdffacture', [
                'factureconfirm' => $decodedResponseConfirmation,
                // 'fileNameqrcode' => $fileNameqrcode,
                'facturedetaille' => $facturedetaille,
                'reffacture' => $reffacture,
                'nomcompletClient' => $nomClient,
                'qrCodeString' => $qrCodeString,
                // 'logoUrl' => $logoUrl,
                'itemFacture' => $itemFacture,
                'montanttotal' => $totalTTC,
                'qrcodecontent' => $qrcodecontent,
                'nim' => $nim,
                'dateTime' => $dateTime,
                'vente' => $vente,
                // 'detailvante' => $vente,
            ]);
        }

    }
 
    // public function storeVente(Request $request) {
    //     $request->validate([
    //         'reference' => 'required|string|unique:vente,reference',
    //         'dateOperation' => 'required|date',
    //         'IFUClient' => 'nullable|string',
    //         'nomClient' => 'nullable|string',
    //         'telClient' => 'nullable|string',
    //         'idModPaie' => 'nullable|exists:mode_paiement,idModPaie',
    //         'lignes' => 'required|array|min:1',
    //         'lignes.*.idP' => 'required|exists:produit,idPro',
    //         'lignes.*.qte' => 'required|integer|min:1',
    //         'lignes.*.montantttc' => 'required|numeric|min:0'
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $vente = Vente::create([
    //             'reference' => $request->reference,
    //             'dateOperation' => $request->dateOperation,
    //             'IFUClient' => $request->IFUClient,
    //             'nomClient' => $request->nomClient,
    //             'telClient' => $request->telClient,
    //             'idModPaie' => $request->idModPaie,
    //             'montantTotal' => 0,
    //             'statutVente' => 'en_attente'
    //         ]);

    //         $montantTotal = 0;
    //         foreach ($request->lignes as $ligne) {
    //             $montantTotal += $ligne['montantttc'];
                
    //             DetailVente::create([
    //                 'idV' => $vente->idV,
    //                 'idP' => $ligne['idP'],
    //                 'quantite' => $ligne['qte'],
    //                 'prixUnitaire' => $ligne['montantttc'] / $ligne['qte'],
    //                 'montantTotal' => $ligne['montantttc']
    //             ]);
    //         }

    //         $vente->update(['montantTotal' => $montantTotal]);
            
    //         DB::commit();
    //         return redirect()->route('ventes')->with('status', 'Vente créée avec succès');
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return redirect()->back()->with('erreur', 'Erreur lors de la création de la vente')->withInput();
    //     }
    // }

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

    public function destroyVente($idV) {
        try {
            $vente = Vente::findOrFail($idV);
            DetailVente::where('idV', $idV)->delete();
            $vente->delete();
            return redirect()->route('ventes')->with('status', 'Vente supprimée avec succès');
        } catch (\Exception $e) {
            return redirect()->route('ventes')->with('erreur', 'Erreur lors de la suppression de la vente');
        }
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
                'stock' => $produit->stocke->quantite ?? 0,
            ]);
        }
        return response()->json([
            'prix' => 0,
            'libelle' => '',
            'taxe' => 0,
        ]);
    }





}
