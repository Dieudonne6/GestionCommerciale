<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fermetures;
use App\Models\DetailFermetures;
use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\Stocke;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FermetureController extends Controller
{

    public function index()
    {
        $ventes = Vente::whereDate('created_at', today())
            ->with('detailVente.produit')
            ->get()
            ->map(function ($vente) {
                return [
                    'id' => $vente->idV,
                    'reference' => $vente->reference,
                    'nomClient' => $vente->nomClient,
                    'nbreProduits' => $vente->detailVente->unique('idPro')->count(),
                    'montantTotal' => $vente->detailVente->sum('montantTTC'),
                    'details' => $vente->detailVente,
                ];
            });

        //  RÉCAP PRODUITS
        $recapProduits = DetailVente::whereHas('vente', function ($q) {
                $q->whereDate('created_at', today());
            })
            ->with('produit')
            ->get()
            ->groupBy('idPro')
            ->map(function ($items) {
                return [
                    'produit' => $items->first()->produit->libelle,
                    'qte' => $items->sum('qte'),
                    'montant' => $items->sum('montantTTC'),
                ];
            });

        return view('pages.fermeture.fermeture', compact('ventes', 'recapProduits'));
    }



    public function store(Request $request)
    {
        $date = now()->toDateString();

        // Vérifier si la fermeture existe déjà
        $existe = Fermetures::where('date', $date)->first();

        if ($existe) {
            return redirect()
            ->route('fermeture.index')
            ->with('error', 'La fermeture de cette journée est déjà effectuée, donc les ventes enrégistrées après la première fermeture ne seront pas comptabilisées dans certaines situations.');
        }

        DB::transaction(function () use ($date) {

            $fermeture = Fermetures::create([
                'idU'  => Auth::id(),
                'date' => $date,
                'heure'=> now()->toTimeString(),
            ]);

            $stocks = Stocke::all();

            foreach ($stocks as $stock) {
                DetailFermetures::create([
                    'idFermeture' => $fermeture->idFermeture,
                    'idPro'       => $stock->idPro,
                    'qteStocke'   => $stock->qteStocke,
                ]);
            }
        });

        return redirect()
            ->route('fermeture.index')
            ->with('success', 'Fermeture de la journée effectuée avec succès ! ');

    }
}
