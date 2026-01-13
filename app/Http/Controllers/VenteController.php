<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\Produit;
use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\FactureNormalisee;
use App\Models\ModePaiement;

class VenteController extends Controller
{
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

    public function storeVente(Request $request) {
        $request->validate([
            'reference' => 'required|string|unique:vente,reference',
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
            $vente = Vente::create([
                'reference' => $request->reference,
                'dateOperation' => $request->dateOperation,
                'IFUClient' => $request->IFUClient,
                'nomClient' => $request->nomClient,
                'telClient' => $request->telClient,
                'idModPaie' => $request->idModPaie,
                'montantTotal' => 0,
                'statutVente' => 'en_attente'
            ]);

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
            return redirect()->route('ventes')->with('status', 'Vente créée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('erreur', 'Erreur lors de la création de la vente')->withInput();
        }
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
            return response()->json(['prix' => $produit->prix]);
        }
        return response()->json(['prix' => 0]);
    }



}
