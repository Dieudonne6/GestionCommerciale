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

class CommandeAchatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user || !$user->idE || !$user->idU) {
            return redirect()->route('login')->with('error', 'Utilisateur non valide ou non connecté.');
        }

        // Récupère toutes les commandes avec fournisseurs et produits
        $commandes    = CommandeAchat::with('fournisseur', 'lignes.produit')->get();
        $fournisseurs = Fournisseur::all();
        $produits     = Produit::all();

        // Charge la vue commandesAchat.blade.php
        return view('pages.Fournisseur&Achat.commandeAchat', compact('commandes', 'fournisseurs', 'produits'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour effectuer cette action.');
        }

        $user = auth()->user();
        if (!$user->idE || !$user->idU) {
            return redirect()->route('login')->with('error', 'Utilisateur non valide.');
        }

        $request->validate([
            'idF'                   => 'required|exists:fournisseurs,idF',
            'reference'             => 'required|string',
            'dateOp'                => 'required|date',
            'delailivraison'        => 'required|string',
            'lignes.*.idPro'        => 'required|exists:produits,idPro',
            'lignes.*.qteCmd'       => 'required|numeric|min:1',
            'lignes.*.prixUnit'     => 'required|numeric|min:0',
            'lignes.*.tva'          => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function() use ($request, $user) {
            // Création de la commande
            $idExercice = Exercice::where('statutExercice', 1)->first()->idExercice; // Exemple pour récupérer l'exercice actif
            $cmd = CommandeAchat::create([
                'idF'               => $request->idF,
                'reference'         => $request->reference,
                'dateOp'            => $request->dateOp,
                'delailivraison'    => $request->delailivraison,
                'statutCom'         => 'En cours',
                'montantTotalHT'    => 0, // Initialisé à 0
                'montantTotalTTC'   => 0, // Initialisé à 0
                'idExercice'        => $idExercice, // Définir dynamiquement l'exercice
                'idE'               => $user->idE, // ID de l'entreprise de l'utilisateur connecté
                'idU'               => $user->idU, // ID de l'utilisateur connecté

            ]);
            $totalHT  = 0;
            $totalTTC = 0;

            // Création des lignes de commande
            foreach ($request->lignes as $ligne) {
                $ht   = $ligne['qteCmd'] * $ligne['prixUnit']; // Montant HT
                $tvaPct = $ligne['tva'] ?? 0; // TVA en pourcentage
                $ttc  = $ht * (1 + $tvaPct / 100); // Montant TTC

                DetailCommandeAchat::create([
                    'idCommande'  => $cmd->idCommande,
                    'idPro'       => $ligne['idPro'],
                    'qteCmd'      => $ligne['qteCmd'],
                    'prixUnit'    => $ligne['prixUnit'],
                    'montantHT'   => $ht,
                    'montantTTC'  => $ttc,
                    'qteRestante' => $ligne['qteCmd'],
                ]);

                $totalHT  += $ht;
                $totalTTC += $ttc;
            }

            // Mise à jour des totaux dans la commande
            $cmd->update([
                'montantTotalHT' => $totalHT,
                'montantTotalTTC' => $totalTTC,
            ]);
        });

        return redirect()->route('commandeAchat.index')->with('status', 'Commande enregistrée.');
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->idE || !$user->idU) {
            return redirect()->route('login')->with('error', 'Utilisateur non valide ou non connecté.');
        }

        $request->validate([
            'idF'                   => 'required|exists:fournisseurs,idF',
            'reference'             => 'required|string',
            'dateOp'                => 'required|date',
            'delailivraison'        => 'required|string',
            'lignes.*.idPro'        => 'required|exists:produits,idP',
            'lignes.*.qteCmd'       => 'required|numeric|min:1',
            'lignes.*.prixUnit'     => 'required|numeric|min:0',
            'lignes.*.tva'          => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function() use ($request, $id) {
            $cmd = CommandeAchat::findOrFail($id);
            $cmd->update($request->only(['idF','reference','dateOp','delailivraison']));

            // Gestion des lignes existantes
            $existingIds = $cmd->lignes->pluck('idDetailCom')->toArray();
            $sentIds     = array_filter(array_column($request->lignes, 'idDetailCom'));
            DetailCommandeAchat::whereIn('idDetailCom', array_diff($existingIds, $sentIds))->delete();

            $totalHT = 0;
            $totalTTC = 0;
            foreach ($request->lignes as $ligne) {
                $ht = $ligne['qteCmd'] * $ligne['prixUnit'];
                $ttc = $ht * (1 + ($ligne['tva'] ?? 0)/100);

                if (!empty($ligne['idDetailCom'])) {
                    $detail = DetailCommandeAchat::find($ligne['idDetailCom']);
                    $detail->update([
                        'idPro' => $ligne['idPro'],
                        'qteCmd' => $ligne['qteCmd'],
                        'prixUnit' => $ligne['prixUnit'],
                        'montantHT' => $ht,
                        'montantTTC' => $ttc,
                        'qteRestante' => $ligne['qteCmd'],
                    ]);
                } else {
                    DetailCommandeAchat::create([
                        'idCommande'  => $cmd->idCommande,
                        'idPro'       => $ligne['idPro'],
                        'qteCmd'      => $ligne['qteCmd'],
                        'prixUnit'    => $ligne['prixUnit'],
                        'montantHT'   => $ht,
                        'montantTTC'  => $ttc,
                        'qteRestante' => $ligne['qteCmd'],
                    ]);
                }

                $totalHT  += $ht;
                $totalTTC += $ttc;
            }
            $cmd->update(['montantTotalHT' => $totalHT, 'montantTotalTTC' => $totalTTC]);
        });

        return redirect()->route('commandeAchat.index')->with('status','Commande mise à jour.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if (!$user || !$user->idE || !$user->idU) {
            return redirect()->route('login')->with('error', 'Utilisateur non valide ou non connecté.');
        }

        $cmd = CommandeAchat::findOrFail($id);
        $cmd->lignes()->delete();
        $cmd->delete();
        return back()->with('status','Commande supprimée.');
    }

    public function deleteLigne($id)
    {
        $user = auth()->user();
        if (!$user || !$user->idE || !$user->idU) {
            return redirect()->route('login')->with('error', 'Utilisateur non valide ou non connecté.');
        }

        DetailCommandeAchat::findOrFail($id)->delete();
        return response()->json(['success'=>true]);
    }
}