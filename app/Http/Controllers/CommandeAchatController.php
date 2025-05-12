<?php

// app/Http/Controllers/CommandeAchatController.php
namespace App\Http\Controllers;

use App\Models\CommandeAchat;
use App\Models\Fournisseur;
use App\Models\Produit;
use App\Models\DetailCommandeAchat;
use App\Models\Exercice;
use App\Models\Entreprise;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommandeAchatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user || !$user->idU) {
            return redirect()->route('login')->with('erreur', 'Utilisateur non valide ou non connecté.');
        }

        // Récupère toutes les commandes avec fournisseurs et produits
        $commandes = CommandeAchat::with(['fournisseur', 'lignes.produit.familleProduit'])
            ->orderBy('dateOp', 'desc')
            ->get();

        $fournisseurs = Fournisseur::all();
        $produits = Produit::all();

        return view('pages.Fournisseur&Achat.commandeAchat', compact('commandes', 'fournisseurs', 'produits'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->back()->with('erreur', 'Vous devez être connecté pour effectuer cette action.');
        }

        $user = auth()->user();
        if (!$user->idU) {
            return redirect()->back()->with('erreur', 'Utilisateur non valide.');
        }

        $validator = Validator::make($request->all(), [
            'idF' => 'required|exists:fournisseurs,idF',
            'reference' => 'required|string|unique:commande_achats,reference',
            'dateOp' => 'required|date',
            'delailivraison' => 'required|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.idPro' => 'required|exists:produits,idPro',
            'lignes.*.qteCmd' => 'required|numeric|min:1',
            'lignes.*.montantHT' => 'required|numeric|min:0',
            'lignes.*.tva' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request, $user) {
                $idExercice = Exercice::where('statutExercice', 1)
                    ->firstOrFail()
                    ->idExercice;

                $cmd = CommandeAchat::create([
                    'idF' => $request->idF,
                    'reference' => $request->reference,
                    'dateOp' => $request->dateOp,
                    'delailivraison' => $request->delailivraison,
                    'statutCom' => 'En cours',
                    'montantTotalHT' => 0,
                    'montantTotalTTC' => 0,
                    'idExercice' => $idExercice,
                    'idU' => $user->idU,
                ]);

                $totalHT = 0;
                $totalTTC = 0;

                foreach ($request->lignes as $ligne) {
                    $ht = floatval($ligne['montantHT']);
                    $tvaPct = floatval($ligne['tva']);
                    $ttc = $ht * (1 + $tvaPct / 100);
                    $prixUnit = $ttc / floatval($ligne['qteCmd']);

                    DetailCommandeAchat::create([
                        'idCommande' => $cmd->idCommande,
                        'idPro' => $ligne['idPro'],
                        'qteCmd' => $ligne['qteCmd'],
                        'montantHT' => $ht,
                        'montantTTC' => $ttc,
                        'tva' => $tvaPct,
                        'prixUnit' => $prixUnit,
                        'qteRestante' => $ligne['qteCmd'],
                    ]);

                    $totalHT += $ht;
                    $totalTTC += $ttc;
                }

                $cmd->update([
                    'montantTotalHT' => round($totalHT, 2),
                    'montantTotalTTC' => round($totalTTC, 2),
                ]);
            });

            return redirect()->route('commandeAchat.index')
                ->with('status', 'Commande enregistrée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('erreur', 'Erreur lors de l\'enregistrement : ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->idU) {
            return redirect()->back()->with('erreur', 'Utilisateur non valide ou non connecté.');
        }

        $validator = Validator::make($request->all(), [
            'idF' => 'required|exists:fournisseurs,idF',
            'reference' => 'required|string|unique:commande_achats,reference,' . $id . ',idCommande',
            'dateOp' => 'required|date',
            'delailivraison' => 'required|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.idPro' => 'required|exists:produits,idPro',
            'lignes.*.qteCmd' => 'required|numeric|min:1',
            'lignes.*.montantHT' => 'required|numeric|min:0',
            'lignes.*.tva' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $cmd = CommandeAchat::findOrFail($id);

                if ($cmd->statutCom !== 'En cours') {
                    throw new \Exception('Cette commande ne peut plus être modifiée.');
                }

                $cmd->update([
                    'idF' => $request->idF,
                    'reference' => $request->reference,
                    'dateOp' => $request->dateOp,
                    'delailivraison' => $request->delailivraison,
                ]);

                $existingIds = $cmd->lignes->pluck('idDetailCom')->toArray();
                $sentIds = array_filter(array_column($request->lignes, 'idDetailCom'));

                DetailCommandeAchat::whereIn('idDetailCom', array_diff($existingIds, $sentIds))->delete();

                $totalHT = 0;
                $totalTTC = 0;

                foreach ($request->lignes as $ligne) {
                    $ht = floatval($ligne['montantHT']);
                    $tvaPct = floatval($ligne['tva']);
                    $ttc = $ht * (1 + $tvaPct / 100);
                    $prixUnit = $ttc / floatval($ligne['qteCmd']);

                    if (!empty($ligne['idDetailCom'])) {
                        $detail = DetailCommandeAchat::find($ligne['idDetailCom']);
                        $detail->update([
                            'idPro' => $ligne['idPro'],
                            'qteCmd' => $ligne['qteCmd'],
                            'montantHT' => $ht,
                            'montantTTC' => $ttc,
                            'tva' => $tvaPct,
                            'prixUnit' => $prixUnit,
                            'qteRestante' => $ligne['qteCmd'],
                        ]);
                    } else {
                        DetailCommandeAchat::create([
                            'idCommande' => $cmd->idCommande,
                            'idPro' => $ligne['idPro'],
                            'qteCmd' => $ligne['qteCmd'],
                            'montantHT' => $ht,
                            'montantTTC' => $ttc,
                            'tva' => $tvaPct,
                            'prixUnit' => $prixUnit,
                            'qteRestante' => $ligne['qteCmd'],
                        ]);
                    }

                    $totalHT += $ht;
                    $totalTTC += $ttc;
                }

                $cmd->update([
                    'montantTotalHT' => round($totalHT, 2),
                    'montantTotalTTC' => round($totalTTC, 2),
                ]);
            });

            return redirect()->route('commandeAchat.index')
                ->with('status', 'Commande mise à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('erreur', 'Erreur lors de la mise à jour : ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if (!$user || !$user->idU) {
            return redirect()->back()->with('erreur', 'Utilisateur non valide ou non connecté.');
        }

        try {
            DB::transaction(function () use ($id) {
                $cmd = CommandeAchat::findOrFail($id);

                if ($cmd->statutCom !== 'En cours') {
                    throw new \Exception('Cette commande ne peut plus être supprimée.');
                }

                $cmd->lignes()->delete();
                $cmd->delete();
            });

            return redirect()->route('commandeAchat.index')
                ->with('status', 'Commande supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('erreur', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    public function deleteLigne($id)
    {
        $user = auth()->user();
        if (!$user || !$user->idU) {
            return redirect()->route('login')->with('error', 'Utilisateur non valide ou non connecté.');
        }

        DB::transaction(function () use ($id) {
            $ligne = DetailCommandeAchat::findOrFail($id);
            $commande = $ligne->commande;

            // Vérifier si la commande peut être modifiée
            if ($commande->statutCom !== 'En cours') {
                throw new \Exception('Cette ligne ne peut plus être supprimée.');
            }

            // Mettre à jour les totaux de la commande
            $commande->update([
                'montantTotalHT' => $commande->montantTotalHT - $ligne->montantHT,
                'montantTotalTTC' => $commande->montantTotalTTC - $ligne->montantTTC
            ]);

            // Supprimer la ligne
            $ligne->delete();
        });

        return response()->json(['success' => true, 'message' => 'Ligne supprimée avec succès.']);
    }

    public function getProduittva($idProduit)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non valide'
                ], 401);
            }

            $produit = Produit::with('familleProduit')
                ->findOrFail($idProduit);

            return response()->json([
                'success' => true,
                'tva' => $produit->familleProduit->TVA ?? 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la TVA : ' . $e->getMessage()
            ], 500);
        }
    }
}