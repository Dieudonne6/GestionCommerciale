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
                $q->whereBetween('created_at', [$dateDebut, $dateFin])
                ->where('statutRecep', 'en cours');
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
