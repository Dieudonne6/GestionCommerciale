<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DetailReceptionCmdAchat;
use App\Models\DetailVente;

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
           ENTRÉES DE STOCK (RÉCEPTIONS)
        ============================ */
        $receptions = DetailReceptionCmdAchat::with([
                'detailCommandeAchat.produit',
                'receptionCmdAchat.commandeAchat.fournisseur'
            ])
            ->whereHas('receptionCmdAchat', function ($q) use ($dateDebut, $dateFin) {
                $q->whereBetween('date', [$dateDebut, $dateFin])
                  ->where('statutRecep', 'en cours'); // réception validée
            })
            ->get();

        /* ============================
           SORTIES DE STOCK (VENTES)
        ============================ */
        $ventes = DetailVente::with(['produit', 'vente'])
            ->whereHas('vente', function ($q) use ($dateDebut, $dateFin) {
                $q->whereBetween('dateOperation', [$dateDebut, $dateFin])
                  ->where('statutVente', 1);
            })
            ->get();

        return view('pages.Inventaire.inventaire', compact(
            'receptions',
            'ventes',
            'dateDebut',
            'dateFin'
        ));
    }
}
