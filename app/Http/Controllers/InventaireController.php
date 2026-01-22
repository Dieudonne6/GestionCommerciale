<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DetailReceptionCmdAchat;
use App\Models\DetailVente;
use App\Models\Fermetures;
use App\Models\DetailFermetures;
use App\Models\Produit;

class InventaireController extends Controller
{
    public function index()
    {
        return view('pages.Inventaire.inventaire');
    }

    public function search(Request $request)
    {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after_or_equal:date_debut',
        ]);

        $dateDebut = $request->date_debut;
        $dateFin   = $request->date_fin;



        /* ============================
        ENTRÉES DE STOCK
        ============================ */
        $receptions = DetailReceptionCmdAchat::with([
            'detailCommandeAchat.produit',
            'receptionCmdAchat.commandeAchat.fournisseur'
        ])
        ->whereHas('receptionCmdAchat', function ($q) use ($dateDebut, $dateFin) {
            $q->whereBetween('date', [$dateDebut, $dateFin])
            ->whereIn('statutRecep', ['en cours', 'complète']);
        })
        ->get();

        


        /* ============================
        SORTIES DE STOCK
        ============================ */
        $ventes = DetailVente::with(['produit', 'vente'])
            ->whereHas('vente', function ($q) use ($dateDebut, $dateFin) {
                $q->whereBetween('dateOperation', [$dateDebut, $dateFin])
                ->where('statutVente', 1);
            })
            ->get();


        // version avec seulement les donnees de l exercice en actif

        // $exercice = Exercice::where('statutExercice', 1)->firstOrFail();

        // $dateDebutExercice = $exercice->dateDebut;
        // $dateFinExercice   = $exercice->dateFin;



        // $receptions = DetailReceptionCmdAchat::with([
        //     'detailCommandeAchat.produit',
        //     'receptionCmdAchat.commandeAchat.fournisseur'
        // ])
        // ->whereHas('receptionCmdAchat', function ($q) use ($dateDebutExercice, $dateFinExercice, $exercice) {
        //     $q->whereBetween('date', [$dateDebutExercice, $dateFinExercice])
        //     ->whereIn('statutRecep', ['en cours', 'complète'])
        //     ->where('idExercice', $exercice->idExercice);
        // })
        // ->get();


        // $ventes = DetailVente::with(['produit', 'vente'])
        // ->whereHas('vente', function ($q) use ($dateDebutExercice, $dateFinExercice, $exercice) {
        //     $q->whereBetween('dateOperation', [$dateDebutExercice, $dateFinExercice])
        //     ->where('statutVente', 1)
        //     ->where('idExercice', $exercice->idExercice);
        // })
        // ->get();


            // 

        /* ============================
        DERNIÈRE FERMETURE AVANT DATE DÉBUT
        ============================ */
        $fermeture = Fermetures::where('date', '<=', $dateDebut)
            ->orderBy('date', 'desc')
            ->with('details')
            ->first();

        $stockInitial = collect();

        if ($fermeture) {
            $stockInitial = $fermeture->details
                ->keyBy('idPro')
                ->map(fn($d) => $d->qteStocke);
        }

        /* ============================
        AGRÉGATION PAR PRODUIT
        ============================ */
        $receptionsParProduit = $receptions->groupBy('detailCommandeAchat.idPro')
            ->map(fn($items) => $items->sum('qteReceptionne'));

        $ventesParProduit = $ventes->groupBy('idPro')
            ->map(fn($items) => $items->sum('qte'));

        $produits = Produit::get();

        $recapProduits = $produits->map(function ($produit) use (
            $stockInitial,
            $receptionsParProduit,
            $ventesParProduit
        ) {
            $initial = $stockInitial[$produit->idPro] ?? 0;
            $recu    = $receptionsParProduit[$produit->idPro] ?? 0;
            $vendu   = $ventesParProduit[$produit->idPro] ?? 0;

            return [
                'produit'        => $produit->libelle,
                'stock_initial'  => $initial,
                'receptionne'    => $recu,
                'vendu'          => $vendu,
                'stock_final'    => $initial + $recu - $vendu,
            ];
        });

        return view('pages.Inventaire.inventaire', compact(
            'receptions',
            'ventes',
            'recapProduits',
            'dateDebut',
            'dateFin'
        ));
    }
}
