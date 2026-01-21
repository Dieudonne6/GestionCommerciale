<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Magasin;
use App\Models\Stocke;

class ProduitStockController extends Controller
{
    public function consulterStocks()
    {
        $user = auth()->user();
        $entrepriseId = $user->idE;
        
        // Récupérer tous les magasins de l'entreprise
        $magasins = Magasin::where('idE', $entrepriseId)->get();
        
        // Récupérer tous les stocks avec les informations des produits et magasins
        $stocks = Stocke::with(['produit', 'magasin'])
            ->whereHas('magasin', function($query) use ($entrepriseId) {
                $query->where('idE', $entrepriseId);
            })
            ->get();
        
        // Statistiques
        $totalProduits = $stocks->count();
        $stockTotal = $stocks->sum('qteStocke');
        $produitsEnRupture = $stocks->filter(function($stock) {
            return $stock->produit && $stock->qteStocke <= ($stock->produit->stockAlert ?? 0);
        })->count();
        
        return view('pages.ProduitStock.consulterStocks', compact('stocks', 'magasins', 'totalProduits', 'stockTotal', 'produitsEnRupture'));
    }
    
    public function ajusterStocks()
    {
        $user = auth()->user();
        $entrepriseId = $user->idE;
        
        // Récupérer tous les magasins de l'entreprise
        $magasins = Magasin::where('idE', $entrepriseId)->get();
        
        // Récupérer tous les stocks avec les informations des produits et magasins
        $stocks = Stocke::with(['produit', 'magasin'])
            ->whereHas('magasin', function($query) use ($entrepriseId) {
                $query->where('idE', $entrepriseId);
            })
            ->get();
        
        return view('pages.ProduitStock.ajusterStocks', compact('stocks', 'magasins'));
    }
    
    public function ajusterStock(Request $request)
    {
        $request->validate([
            'idStocke' => 'required|exists:stockes,idStocke',
            'typeAjustement' => 'required|in:entree,sortie',
            'quantite' => 'required|integer|min:1',
            'motif' => 'required|string|max:255'
        ]);
        
        try {
            $stock = Stocke::with('produit')->findOrFail($request->idStocke);
            
            if (!$stock->produit) {
                return back()->with('erreur', 'Ce stock n\'est associé à aucun produit valide.');
            }
            
            $ancienneQuantite = $stock->qteStocke;
            
            if ($request->typeAjustement === 'entree') {
                $stock->qteStocke += $request->quantite;
                $typeOperation = 'Entrée';
            } else {
                if ($stock->qteStocke < $request->quantite) {
                    return back()->with('erreur', 'La quantité en stock est insuffisante pour cette sortie.');
                }
                $stock->qteStocke -= $request->quantite;
                $typeOperation = 'Sortie';
            }
            
            $stock->save();
            
            return back()->with('status', "Ajustement de stock effectué avec succès: $typeOperation de $request->quantite unités. Ancienne quantité: $ancienneQuantite, Nouvelle quantité: {$stock->qteStocke}");
            
        } catch (\Exception $e) {
            return back()->with('erreur', 'Une erreur est survenue lors de l\'ajustement du stock: ' . $e->getMessage());
        }
    }
    
    public function getStockDetails($idStocke)
    {
        try {
            $stock = Stocke::with(['produit', 'magasin'])->findOrFail($idStocke);
            
            // Vérifier si les relations existent
            if (!$stock->produit) {
                return response()->json(['error' => 'Produit non trouvé'], 404);
            }
            
            if (!$stock->magasin) {
                return response()->json(['error' => 'Magasin non trouvé'], 404);
            }
            
            // Préparer les données sans les champs binaires problématiques
            $data = [
                'idStocke' => $stock->idStocke,
                'qteStocke' => $stock->qteStocke,
                'CUMP' => $stock->CUMP,
                'idPro' => $stock->idPro,
                'idMag' => $stock->idMag,
                'produit' => [
                    'idPro' => $stock->produit->idPro,
                    'libelle' => $stock->produit->libelle,
                    'prix' => $stock->produit->prix,
                    'desc' => $stock->produit->desc,
                    'stockAlert' => $stock->produit->stockAlert,
                    'stockMinimum' => $stock->produit->stockMinimum,
                    'image' => $stock->produit->image ? true : false // Juste un indicateur
                ],
                'magasin' => [
                    'idMag' => $stock->magasin->idMag,
                    'libelle' => $stock->magasin->libelle
                ]
            ];
            
            return response()->json($data);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Stock non trouvé'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }
    
    public function getProduitImage($idPro)
    {
        try {
            $produit = Produit::findOrFail($idPro);
            
            if (!$produit->image) {
                return response()->json(['error' => 'Aucune image'], 404);
            }
            
            // Retourner l'image en base64
            $imageData = base64_encode($produit->image);
            $imageSrc = "data:image/jpeg;base64," . $imageData;
            
            return response()->json(['imageSrc' => $imageSrc]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Produit non trouvé'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }
}
