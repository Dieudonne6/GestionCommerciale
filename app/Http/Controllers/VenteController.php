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
use App\Models\CategorieTarifaire;
use Illuminate\Support\Arr;

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
        $categories = CategorieTarifaire::where('actif', 1)->get();

        $categoriesJS = $categories->map(function ($c) {
            return [
                'id' => $c->id,
                'type' => $c->type_reduction,
                'value' => $c->valeur_reduction,
            ];
        });

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
        
        return view('pages.Facturation.ventes', compact('allClients', 'allproduits', 'allVente', 'modes', 'regimeEntreprise', 'numVente','categories','categoriesJS'));
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

        $categorieId = $request->input('categorie_tarifaire_id'); // peut être null
        $categorie = $categorieId ? CategorieTarifaire::find($categorieId) : null;
        $aib = $categorie->aib;
        // dd($ifuEntreprise, $tokenEntreprise);
        // preparation des items de la facture

        // ---- 1) recalculer les lignes à partir de la base (sécurité) ----

        $totalTTC_calc = 0;
        $lineData = []; 

        foreach ($lignes as $article) {
            $prod = Produit::with('familleProduit')->find($article['idP']);
            if (!$prod) return back()->withErrors("Produit introuvable");

            $qty = intval($article['qte']);
            $unitPrice = intval($prod->prix); // prix unitaire TTC depuis la base
            $lineTotal = $unitPrice * $qty;

            $lineData[] = [
                'idP' => $prod->idPro,
                'libelle' => $prod->libelle,
                'qty' => $qty,
                'unitPrice' => $unitPrice,
                'lineTotal' => $lineTotal,
                'taxGroup' => $prod->familleProduit->groupe ?? 0,
            ];

            $totalTTC_calc += $lineTotal;
        }


        // ---- 2) calcul réduction totale selon catégorie ----
        $discount_total = 0;
        if ($categorie) {
            if ($categorie->type_reduction === 'pourcentage') {
                $discountTotal = (int) round(
                    ($totalTTC_calc * $categorie->valeur_reduction) / 100
                );            
            } else { // 'fixe'
                $discountTotal = min(
                            (int) $categorie->valeur_reduction,
                            $totalTTC_calc
                );            
            }
        }


        // ---- 3) répartir la réduction proportionnellement sur les lignes ----
        $distributed = [];
        $remaining = $discountTotal;
        foreach ($lineData as $i => $ln) {
            if ($totalTTC_calc > 0) {
                $share = intdiv(
                    $ln['lineTotal'] * $discountTotal,
                    $totalTTC_calc 
                 );           
            } else {
                $share = 0;
            }
            $distributed[$i] = $share;
            $remaining -= $share;
        }

        // Les divisions entières laissent toujours un reste → on le corrige sur la 1ʳᵉ ligne
        if (!empty($distributed) && $remaining !== 0) {
            $distributed[0] += $remaining;
        }


        // ancienne logique sans la reduction
        // $items = []; // Initialiser un tableau vide pour les éléments

        
        // foreach ($lignes as $article ) {
        //     $items[] = [
        //         // 'name' => 'Frais cantine pour : ' . $mois, 
        //         'name' => $article['libelle'], 
        //         // 'price' => intval($montantmoiscontrat),
        //         'price' => intval($article['prixU']),
        //         'quantity' => intval($article['qte']),
        //         'taxGroup' => $article['taxe'],
        //     ];
        // }


        // // Préparez les données JSON pour l'API
        // $jsonData = json_encode([
        //     "ifu" => $ifuEntreprise, // ici on doit rendre la valeur de l'ifu dynamique
        //     // "aib" => "A",
        //     "type" => 'FV',
        //     "items" => $items,

        //     "client" => [
        //         "ifu" => $IFUClient ?? '',
        //         "name" =>  $nomClient ?? '',
        //         "contact" => $telClient ?? '',
        //         // "address"=> "string"
        //     ],
        //     "operator" => [
        //         "name" => "CRYSTAL SERVICE INFO (TONY ABAMAN FIRMIN)"
        //     ],
        //     "payment" => [
        //         [
        //             "name" => $libellModepaie ?? "ESPECES",  // mettre $ModPaie et s'assurer qu'il correspond a ('ESPECES, CHEQUE, AUTRE)
        //             "amount" => intval($totalTTC)
        //         ]
        //     ],
        // ]);


        // nouvelle logique avec la reduction

        // ---- 4) construire le tableau $items (pour API) et préparer enregistrement DB ----
        $items = [];
        $totalTTC_after = 0;

        foreach ($lineData as $i => $ln) {
            $line_discount = $distributed[$i] ?? 0;
            $new_line_total = $ln['lineTotal'] - $line_discount;
            if ($new_line_total < 0) $new_line_total = 0;

            // nouveau prix unitaire (TTC)
            $qty = max(1, intval($ln['qty'])); // s'assurer >=1 pour la logique (si qty 0, on ignore plus bas)
            if ($qty === 0) continue;

            // base price par unité (floor)
            $base_unit = intdiv($new_line_total, $qty);
            $remainder = $new_line_total - ($base_unit * $qty); // remainder < qty

            // si remainder == 0 : on envoie un seul item (price=base_unit, qty=qty)
            if ($remainder === 0) {
                $items[] = [
                    'name' => $ln['libelle'],
                    'price' => (int) $base_unit,
                    'quantity' => (int) $qty,
                    'taxGroup' => $ln['taxGroup'],
                ];
            } else {
                // si qty>1 : envoyer (qty-1) x base_unit + 1 x (base_unit + remainder)
                if ($qty > 1) {
                    $items[] = [
                        'name' => $ln['libelle'],
                        'price' => (int) $base_unit,
                        'quantity' => (int) ($qty - 1),
                        'taxGroup' => $ln['taxGroup'],
                    ];
                    $items[] = [
                        'name' => $ln['libelle'],
                        'price' => (int) ($base_unit + $remainder),
                        'quantity' => 1,
                        'taxGroup' => $ln['taxGroup'],
                    ];
                } else {
                    // qty == 1, donc base_unit = 0 et remainder = new_line_total => on envoie une seule ligne
                    $items[] = [
                        'name' => $ln['libelle'],
                        'price' => (int) $new_line_total,
                        'quantity' => 1,
                        'taxGroup' => $ln['taxGroup'],
                    ];
                }
            }



            // stocker pour enregistrement DB : on conserve new_unit_price pour affichage/enregistrement
            $lineData[$i]['discount'] = $line_discount;
            $lineData[$i]['new_line_total'] = $new_line_total;
            $lineData[$i]['base_unit'] = $base_unit;
            $lineData[$i]['remainder'] = $remainder;

            $totalTTC_after += $new_line_total;
        }

        if ($aib && $aib == 1) {
            // dd('aib oui');
            
            $jsonData = json_encode([
                "ifu" => $ifuEntreprise,
                "type" => 'FV',
                "aib" => 'A',
                "items" => $items,
                "client" => [
                    "ifu" => $IFUClient ?? '',
                    "name" =>  $nomClient ?? '',
                    "contact" => $telClient ?? '',
                ],
                "operator" => [
                    "name" => "CRYSTAL SERVICE INFO (TONY ABAMAN FIRMIN)"
                ],
                "payment" => [
                    [
                        "name" => $libellModepaie ?? "ESPECES",
                        "amount" => intval($totalTTC_after)
                    ]
                ],
            ]);

        } else {
            // dd('aib non');

            $jsonData = json_encode([
                "ifu" => $ifuEntreprise,
                "type" => 'FV',
                // "aib" => 'A',
                "items" => $items,
                "client" => [
                    "ifu" => $IFUClient ?? '',
                    "name" =>  $nomClient ?? '',
                    "contact" => $telClient ?? '',
                ],
                "operator" => [
                    "name" => "CRYSTAL SERVICE INFO (TONY ABAMAN FIRMIN)"
                ],
                "payment" => [
                    [
                        "name" => $libellModepaie ?? "ESPECES",
                        "amount" => intval($totalTTC_after)
                    ]
                ],
            ]);

        }
        



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
            
            // if ($regimeEntreprise == 'TPS') {
            //     $TotalTVA = 0;
            // } else {
            //     $TotalTVA = -(ceil($totalTTC_after / 1.18) - $totalTTC_after);
            // }


            if ($regimeEntreprise === 'TPS') {
                $totalHT_after = $totalTTC_after;
                $TotalTVA = 0;
            } else {
                // HT approximé en entier : HT = intdiv(TTC * 100, 118)
                // $totalHT_after = intdiv($totalTTC_after * 100, 118);
                $totalHT_after = (int) round($totalTTC_after * 100 / 118);
                $TotalTVA = $totalTTC_after - $totalHT_after;
            }
            
            $totalfinal = $totalTTC_after;
            $montant_aib = 0;

            if ($aib && $aib == 1) {
                $montant_aib = ceil($totalHT_after * 0.01);
                $totalfinal = $totalTTC_after + $montant_aib;
            } else {
                $montant_aib = 0;
            }
            

            $referenceVente = $reference;
            // dd($datezzSansHeure);
            // Debut de la transaction
            DB::transaction(function () use (
                $dateOperation,
                $totalTTC_after, 
                $totalfinal, 
                $montant_aib, 
                $totalHT_after,
                $totalTTC_calc,
                $discountTotal,
                $IFUClient,
                $nomClient,
                $telClient,
                $userId,
                $entrepriseId,
                $idExercice,
                $idModPaie,
                $lineData,
                $distributed,
                $categorieId,
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
                // vente

                $total_brut = isset($totalTTC_calc) ? round($totalTTC_calc, 2) : 0.00;
                $total_reduction = isset($discountTotal) ? round($discountTotal, 2) : 0.00;
                // si totalTTC_after est défini, privilégier ; sinon calculer brut - reduction
                $total_net = isset($totalTTC_after) ? round($totalfinal, 2) : round(max(0, $total_brut - $total_reduction) + $montant_aib, 2);

                $vente = new Vente();
                $vente->dateOperation = $dateOperation;
                $vente->montantTotal = intval($totalfinal);
                $vente->montant_aib = intval($montant_aib);
                $vente->reference = $referenceVente;
                $vente->statutVente = 1;
                $vente->IFUClient = $IFUClient;
                $vente->nomClient = $nomClient;
                $vente->telClient = $telClient;
                $vente->idU = intval($userId);
                $vente->idE = intval($entrepriseId);
                $vente->idExercice = intval($idExercice);
                $vente->idModPaie = intval($idModPaie);
                // stocker la categorie tarifaire si tu as le champ categorie_tarifaire_id sur la table ventes
                if ($categorieId) $vente->categorie_tarifaire_id = $categorieId;
                $vente->total_brut = $total_brut;
                $vente->total_reduction = $total_reduction;
                $vente->total_net = $total_net;
                $vente->save();

                // =========================
                // DETAILS DE VENTE
                // =========================
                // Details : il faut recréer les mêmes "groupes" d'items qu'on a envoyé à l'API
                        // On parcourt lineData et on reconstruit selon distributed/base/remainder
                        foreach ($lineData as $i => $ln) {
                            $qty = intval($ln['qty']);
                            $new_line_total = intval($ln['new_line_total'] ?? 0);
                            // valeurs d'origine (brut)
                            $brut_unit = intval($ln['unitPrice']);      // prix unitaire brut (TTC tel que en base)
                            $brut_total = intval($ln['lineTotal']);     // total brut ligne = brut_unit * qty

                            if ($qty <= 0) continue;

                            $base_unit = intval($ln['base_unit'] ?? intdiv($new_line_total, max(1, $qty)));
                            $remainder = intval($ln['remainder'] ?? ($new_line_total - ($base_unit * $qty)));

                            if ($remainder === 0) {
                                // enregistrement simple : prix unitaire = base_unit
                                $unit_net = $base_unit; // prix unitaire net
                                $total_net = $new_line_total;
                                DetailVente::create([
                                    'qte' => $qty,
                                    'prixUnit' => $base_unit,
                                    'montantHT' => $regimeEntreprise === 'TPS' ? intval($new_line_total) : round($new_line_total / 1.18, 2),
                                    'montantTTC' => intval($new_line_total),
                                    'idPro' => intval($ln['idP']),
                                    'idV' => $vente->idV,

                                    // nouveaux champs
                                    'prix_unitaire_brut' => intval($brut_unit),
                                    'total_ligne_brut' => intval($brut_total),
                                    'reduction_ligne' => intval($brut_total - $total_net),
                                    'total_ligne_net' => intval($total_net),
                                    'prix_unitaire_net' => intval($unit_net),
                                ]);
                            } else {
                                // si remainder > 0 et qty>1, on crée deux enregistrements pour matcher ce qu'on envoie à l'API
                                if ($qty > 1) {
                                    // qty-1 lignes à base_unit
                                    $qty1 = $qty - 1;
                                    $total_net_part1 = $base_unit * $qty1;
                                    $brut_total_part1 = $brut_unit * $qty1;

                                    DetailVente::create([
                                        'qte' => $qty - 1,
                                        'prixUnit' => $base_unit,
                                        'montantHT' => $regimeEntreprise === 'TPS' ? round(($base_unit * ($qty - 1))) : round(($base_unit * ($qty - 1)) / 1.18, 2),
                                        'montantTTC' => intval($base_unit * ($qty - 1)),
                                        'idPro' => intval($ln['idP']),
                                        'idV' => $vente->idV,

                                        // nouveaux champs
                                        'prix_unitaire_brut' => intval($brut_unit),
                                        'total_ligne_brut' => intval($brut_total_part1),
                                        'reduction_ligne' => intval($brut_total_part1 - $total_net_part1),
                                        'total_ligne_net' => intval($total_net_part1),
                                        'prix_unitaire_net' => intval($base_unit),
                                    ]);
                                    // 1 ligne avec base_unit + remainder
                                    $last_price = $base_unit + $remainder;
                                    $brut_total_last = $brut_unit * 1;
                                    $total_net_last = $last_price; // parce que qte=1
                                    DetailVente::create([
                                        'qte' => 1,
                                        'prixUnit' => $last_price,
                                        'montantHT' => $regimeEntreprise === 'TPS' ? round($last_price, 2) : round($last_price / 1.18, 2),
                                        'montantTTC' => intval($last_price),
                                        'idPro' => intval($ln['idP']),
                                        'idV' => $vente->idV,

                                        // nouveaux champs
                                        'prix_unitaire_brut' => intval($brut_unit),
                                        'total_ligne_brut' => intval($brut_total_last),
                                        'reduction_ligne' => intval($brut_total_last - $total_net_last),
                                        'total_ligne_net' => intval($total_net_last),
                                        'prix_unitaire_net' => intval($last_price),
                                    ]);
                                } else {
                                    // qty == 1
                                    $total_net_single = $new_line_total;
                                    DetailVente::create([
                                        'qte' => 1,
                                        'prixUnit' => intval($new_line_total),
                                        'montantHT' => $regimeEntreprise === 'TPS' ? round($new_line_total, 2) : round($new_line_total / 1.18 , 2),
                                        'montantTTC' => intval($new_line_total),
                                        'idPro' => intval($ln['idP']),
                                        'idV' => $vente->idV,

                                        // nouveaux champs
                                        'prix_unitaire_brut' => intval($brut_unit),
                                        'total_ligne_brut' => intval($brut_total),
                                        'reduction_ligne' => intval($brut_total - $total_net_single),
                                        'total_ligne_net' => intval($total_net_single),
                                        'prix_unitaire_net' => intval($total_net_single),
                                    ]);
                                }
                            }
                        }

                // =========================
                // MISE À JOUR DU STOCK
                // =========================
                foreach ($lineData as $ln) {
                    $produit = Produit::with('stocke')->findOrFail($ln['idP']);
                    $produit->stocke->decrement('qteStocke', $ln['qty']);
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
                $facturenormalise->montantTotal = intval($totalfinal);
                $facturenormalise->montantTotalTTC = intval($totalfinal);
                $facturenormalise->TotalTVA = intval($TotalTVA);
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
                'montanttotal' => intval($totalfinal),
                'montantaib' => intval($montant_aib),
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
        } else {
            return back()->with('erreur', 'Erreur API : token ou ifu ou item invalide ');
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

            // dd($facture->vente->montant_aib);

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
                'montantaib' => $facture->vente->montant_aib,
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
            $montantaib = $infoVente->montant_aib;

            $item = $factureoriginale->itemFacture; // Initialiser un tableau vide pour les éléments
            $items= json_decode($item, true);

            // dd($items);

            if ($montantaib > 0) {
                // aib existe

                // Préparez les données JSON pour l'API
                $jsonData = json_encode([
                    "ifu" => $ifuentreprise, // ici on doit rendre la valeur de l'ifu dynamique
                    "aib" => "A",
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
            } else {
                // pas d'aib

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
            }
            


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
                'montantaib' => $factureoriginale->vente->montant_aib,
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

            } else {
                return back()->with('erreur', 'Erreur API : token ou ifu ou item invalide ');
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
