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
            'idMagSource' => 'required|different:idMagDestination|exists:magasins,idMag',
            'idMagDestination' => 'required|exists:magasins,idMag',
            'dateTransfert' => 'required|date',
            'produits' => 'required|string',
            'motif' => 'required|string|max:255'
        ]);
        
        try {
            DB::beginTransaction();
            
            $user = auth()->user();
            $entrepriseId = $user->idE;
            
            // Décoder les produits depuis le JSON
            $produits = json_decode($request->produits, true);
            
            if (empty($produits)) {
                return back()->with('erreur', 'Aucun produit spécifié pour le transfert')->withInput();
            }
            
            // Vérifier que les magasins appartiennent à l'entreprise
            $magasinSource = Magasin::where('idMag', $request->idMagSource)
                ->where('idE', $entrepriseId)
                ->firstOrFail();
            
            $magasinDestination = Magasin::where('idMag', $request->idMagDestination)
                ->where('idE', $entrepriseId)
                ->firstOrFail();
            
            // Créer le transfert
            $transfert = TransfertMagasin::create([
                'dateTransfert' => $request->dateTransfert,
                'referenceTransfert' => $this->genererReference(),
                'idMag' => $request->idMagDestination, // Magasin de destination
                'idMagSource' => $request->idMagSource, // Magasin source
            ]);
            
            $totalTransfere = 0;
            
            // Traiter chaque produit
            foreach ($produits as $produitData) {
                $quantite = $produitData['quantite'];
                $idPro = $produitData['idPro'];
                
                // Vérifier le stock disponible dans le magasin source
                $stockSource = Stocke::where('idPro', $idPro)
                    ->where('idMag', $request->idMagSource)
                    ->first();
                
                if (!$stockSource || $stockSource->qteStocke < $quantite) {
                    DB::rollBack();
                    return back()->with('erreur', 'Stock insuffisant pour le produit ID: ' . $idPro . '. Disponible: ' . ($stockSource ? $stockSource->qteStocke : 0) . ', Demandé: ' . $quantite);
                }
                
                // Déduire du stock source
                $stockSource->qteStocke -= $quantite;
                $stockSource->save();
                
                // Ajouter au stock destination
                $stockDestination = Stocke::where('idPro', $idPro)
                    ->where('idMag', $request->idMagDestination)
                    ->first();
                
                if ($stockDestination) {
                    $stockDestination->qteStocke += $quantite;
                    $stockDestination->save();
                } else {
                    // Créer le stock s'il n'existe pas
                    Stocke::create([
                        'idPro' => $idPro,
                        'idMag' => $request->idMagDestination,
                        'qteStocke' => $quantite,
                        'CUMP' => $stockSource->CUMP ?? 0,
                    ]);
                }
                
                // Créer le détail du transfert
                DetailTransfertMagasin::create([
                    'qteTransferer' => $quantite,
                    'idPro' => $idPro,
                    'idTransMag' => $transfert->idTransMag,
                ]);
                
                $totalTransfere += $quantite;
            }
            
            DB::commit();
            
            return redirect()->route('transferts.index')
                ->with('status', "Transfert effectué avec succès. Référence: {$transfert->referenceTransfert}. Total produits transférés: $totalTransfere");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('erreur', 'Une erreur est survenue lors du transfert: ' . $e->getMessage())
                ->withInput();
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
