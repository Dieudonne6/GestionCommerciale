<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransfertMagasin;
use App\Models\DetailTransfertMagasin;
use App\Models\Stocke;
use App\Models\Magasin;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TransfertMagasinController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $entrepriseId = $user->idE;
        
        // Récupérer tous les magasins de l'entreprise
        $magasins = Magasin::where('idE', $entrepriseId)->get();
        
        // Récupérer tous les transferts avec leurs détails
        $transferts = TransfertMagasin::with(['magasin', 'magasinSource', 'details.produit'])
            ->whereHas('magasin', function($query) use ($entrepriseId) {
                $query->where('idE', $entrepriseId);
            })
            ->orderBy('dateTransfert', 'desc')
            ->get();
        
        return view('pages.ProduitStock.transfertMagasins', compact('magasins', 'transferts'));
    }
    
    public function create()
    {
        $user = auth()->user();
        $entrepriseId = $user->idE;
        
        // Récupérer tous les magasins de l'entreprise
        $magasins = Magasin::where('idE', $entrepriseId)->get();
        
        // Récupérer tous les produits sans relations complexes
        $produits = Produit::select('idPro', 'libelle', 'prix', 'stockAlert', 'stockMinimum')->get();
        
        return view('pages.ProduitStock.createTransfert', compact('magasins', 'produits'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'idMagSource' => 'required|different:idMagDestination',
            'idMagDestination' => 'required',
            'produits' => 'required|string',
            'motif' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $produits = json_decode($request->produits, true);

            $transfert = TransfertMagasin::create([
                'dateTransfert' => $request->dateTransfert,
                'referenceTransfert' => $this->genererReference(),
                'idMag' => $request->idMagDestination,
                'idMagSource' => $request->idMagSource,
            ]);

            foreach ($produits as $p) {

                $stock = Stocke::where('idStocke', $p['idStocke'])
                    ->where('idMag', $request->idMagSource)
                    ->firstOrFail();

                //  LE TRANSFERT
                $stock->idMag = $request->idMagDestination;
                $stock->save();

                DetailTransfertMagasin::create([
                    'idStocke' => $stock->idStocke,
                    'idPro' => $stock->idPro,
                    'qteTransferer' => $stock->qteStocke, 
                    'idTransMag' => $transfert->idTransMag,
                ]);
            }

            DB::commit();

            return redirect()->route('transferts')
                ->with('status', 'Transfert effectué avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('erreur', $e->getMessage());
        }
    }

    
    public function show($idTransMag)
    {
        $user = auth()->user();
        $entrepriseId = $user->idE;
        
        $transfert = TransfertMagasin::with(['magasin', 'magasinSource', 'details.produit'])
            ->whereHas('magasin', function($query) use ($entrepriseId) {
                $query->where('idE', $entrepriseId);
            })
            ->findOrFail($idTransMag);
        
        return view('pages.ProduitStock.showTransfert', compact('transfert'));
    }
    
    public function showDetails($idTransMag)
    {
        $user = auth()->user();
        $entrepriseId = $user->idE;
        
        $transfert = TransfertMagasin::with(['magasin', 'magasinSource', 'details.produit'])
            ->whereHas('magasin', function($query) use ($entrepriseId) {
                $query->where('idE', $entrepriseId);
            })
            ->findOrFail($idTransMag);
        
        return view('pages.ProduitStock.showTransfert', compact('transfert'));
    }
    
    public function getStocksByMagasin($idMag)
    {
        $user = auth()->user();
        $entrepriseId = $user->idE;
        
        // Vérifier que le magasin appartient à l'entreprise
        $magasin = Magasin::where('idMag', $idMag)
            ->where('idE', $entrepriseId)
            ->first();
            
        if (!$magasin) {
            return response()->json(['error' => 'Magasin non trouvé ou non autorisé'], 404);
        }
        
        // Récupérer les stocks de ce magasin avec jointure simple
        $stocks = DB::table('stockes')
            ->join('produits', 'stockes.idPro', '=', 'produits.idPro')
            ->where('stockes.idMag', $idMag)
            ->where('stockes.qteStocke', '>', 0)
            ->select('stockes.idStocke', 'stockes.idPro', 'produits.libelle', 'stockes.qteStocke', 'stockes.CUMP')
            ->get();
        
        return response()->json($stocks);
    }
    
    private function genererReference()
    {
        $prefix = 'TRF';
        $date = date('Ymd');
        $lastTransfert = TransfertMagasin::whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->first();
        
        $sequence = $lastTransfert ? intval(substr($lastTransfert->referenceTransfert, -4)) + 1 : 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
