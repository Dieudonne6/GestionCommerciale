<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;
use DateTime;
use Carbon\Carbon;
use App\Models\Produit;
use App\Models\Vente;
use App\Models\Exercice;
use App\Models\Proforma;
use App\Models\DetailProforma;

class ProformatController extends Controller
{

    private function genererNumeroProforma(): string
    {
        $annee = Carbon::now()->format('Y');

        // Récupérer la dernière vente de l'année
        $lastProforma = Proforma::where('reference', 'like', "PFOR-$annee-%")
            ->orderBy('reference', 'desc')
            ->first();

        if ($lastProforma) {
            // Extraire le compteur
            $lastNumber = intval(substr($lastProforma->reference, -9));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('PFOR-%s-%09d', $annee, $nextNumber);
    }


    public function index(){

        $allproduits = Produit::get();
        $numProforma = $this->genererNumeroProforma();
        $exerciceActif = Exercice::where('statutExercice', 1)->firstOrFail();
        $allProforma = Proforma::where('idExercice', $exerciceActif->idExercice)
            ->with('client', 'detailProforma')
            ->get();

        $user = auth()->user();
        $entreprise = $user->entreprise;
        $regimeEntreprise = $entreprise ? $entreprise->regime : 'normal';

        return view ('pages.Facturation.proformat', compact('allproduits', 'regimeEntreprise', 'numProforma', 'allProforma'));
    }

   public function storeProforma(Request $request) {

        // donnees de la vue
        $nomClient = $request->input('nomClient');
        $telClient = $request->input('telClient');
        $reference = $request->input('numFacture');
        $dateOperation = $request->input('dateOperation');
        $totalHT = $request->input('totalHT');
        $totalTTC = $request->input('totalTTC');
        $lignes = $request->input('lignes');

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
        $logoUrl = $entreprise->logo;

        // Enregistrer la vente
        $idExercice = Exercice::where('statutExercice', 1)
            ->firstOrFail()
            ->idExercice;

        // dd($lignes, $nomClient, $telClient, $reference, $dateOperation, $totalHT, $totalTTC);

        if ($regimeEntreprise == 'TPS') {
            $TotalTVA = 0;
        } else {
            $TotalTVA = -(ceil($totalTTC / 1.18) - $totalTTC);
        }

        // Debut de la transaction
        DB::transaction(function () use (
            $dateOperation,
            $totalTTC,
            $TotalTVA,
            $nomClient,
            $telClient,
            $userId,
            $entrepriseId,
            $idExercice,
            $lignes,
            $reference,
            $regimeEntreprise,
        ) {

            // =========================
            // ENREGISTREMENT VENTE
            // =========================
            $proforma = new Proforma();
            $proforma->dateOperation = $dateOperation;
            $proforma->montantTotal = intval($totalTTC);
            $proforma->TotalTVA = intval($TotalTVA);
            $proforma->reference = $reference;
            $proforma->nomClient = $nomClient;
            $proforma->telClient = $telClient;
            // $proforma->idU = intval($userId);
            // $proforma->idE = intval($entrepriseId);
            $proforma->idExercice = intval($idExercice);
            $proforma->save();

            // =========================
            // DETAILS DU PROFORMA
            // =========================
            foreach ($lignes as $article) {
                DetailProforma::create([
                    'qteProforma' => intval($article['qte']),
                    'prixUnit' => intval($article['prixU']),
                    'montantHT' => ceil($article['montantht']),
                    'montantTTC' => intval($article['montantttc']),
                    'idPro' => intval($article['idP']),
                    'idProforma' => $proforma->idProforma,
                ]);
            }

        });



        return view('pages.Fournisseur&Achat.test', [
            // 'fileNameqrcode' => $fileNameqrcode,
            'reference' => $reference,
            'nomcompletClient' => $nomClient,
            'telClient' => $telClient,
            'logoUrl' => $logoUrl,
            'lignes' => $lignes,
            'regime' => $regimeEntreprise,
            'montanttotal' => $totalTTC,
            'TotalTVA' => $TotalTVA,
            'TotalHT' => ceil($totalTTC - $TotalTVA),
            'dateOperation' => $dateOperation,
            'nomEntreprise' => $nomEntreprise,
            'adresseEntreprise' => $adresseEntreprise,
            'telEntreprise' => $telEntreprise,
            'mailEntreprise' => $mailEntreprise,
            'IFUEntreprise' => $ifuEntreprise,
        ]);

   } 

   public function deleteProforma(Request $request, $idProforma) {

        Proforma::where('idProforma', $idProforma)->delete();

        return back()->with('status', "Le proforma a été supprimé avec success.");

   }

   public function duplicataproformat($idProforma) {

        $infoProforma = Proforma::where('idProforma', $idProforma)
        ->with('detailProforma')
        ->first();

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
        $logoUrl = $entreprise->logo;
        $lignes = $infoProforma->detailProforma;

        return view('pages.Fournisseur&Achat.test', [
            // 'fileNameqrcode' => $fileNameqrcode,
            'reference' => $infoProforma->reference,
            'nomcompletClient' => $infoProforma->nomClient,
            'telClient' => $infoProforma->telClient,
            'logoUrl' => $logoUrl,
            'lignes' => $lignes,
            'regime' => $regimeEntreprise,
            'montanttotal' => $infoProforma->montantTotal,
            'TotalTVA' => $infoProforma->TotalTVA,
            'TotalHT' => ceil(($infoProforma->montantTotal) - ($infoProforma->TotalTVA)),
            'dateOperation' => $infoProforma->dateOperation,
            'nomEntreprise' => $nomEntreprise,
            'adresseEntreprise' => $adresseEntreprise,
            'telEntreprise' => $telEntreprise,
            'mailEntreprise' => $mailEntreprise,
            'IFUEntreprise' => $ifuEntreprise,
        ]);

        // dd($idProforma);
   }
}
