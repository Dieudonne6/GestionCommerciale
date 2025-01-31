<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Caise;
use App\Models\Commande; 
use App\Models\Magasin; 
use Illuminate\Http\Request;
use App\Models\Reception;
use App\Models\LigneReception;
use App\Models\LigneCommande;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        $caisses = Caise::all();
        return view('pages.definition.caisses', compact('caisses'));
    }

    public function store(Request $request)
    {
        // Validation avec gestion des doublons
        $request->validate([
            'codeCais' => 'required|string|unique:caises,codeCais|max:255',
            'libelleCais' => 'required|string|max:255',
        ], [
            'codeCais.required' => 'Le code de la caisse est obligatoire.',
            'codeCais.unique' => 'Ce code existe déjà. Veuillez en saisir un autre.',
            'libelleCais.required' => 'Le libellé de la caisse est obligatoire.',
        ]);

        // Création de la caisse
        Caise::create([
            'codeCais' => $request->codeCais,
            'libelleCais' => $request->libelleCais,
        ]);

        return redirect()->back()->with('success', 'Caisse ajoutée avec succès.');
    }

    public function update(Request $request, $id)
    {
        // Validation avec gestion des doublons lors de la modification
        $request->validate([
            'codeCais' => 'required|string|max:255|unique:caises,codeCais,' . $id . ',idCais',
            'libelleCais' => 'required|string|max:255',
        ], [
            'codeCais.required' => 'Le code de la caisse est obligatoire.',
            'codeCais.unique' => 'Ce code existe déjà pour une autre caisse.',
            'libelleCais.required' => 'Le libellé de la caisse est obligatoire.',
        ]);

        // Mise à jour de la caisse
        $caisse = Caise::findOrFail($id);
        $caisse->update([
            'codeCais' => $request->codeCais,
            'libelleCais' => $request->libelleCais,
        ]);

        return redirect()->back()->with('success', 'Caisse modifiée avec succès.');
    }

    public function destroy($id)
    {
        // Suppression de la caisse
        $caisse = Caise::findOrFail($id);
        $caisse->delete();

        return redirect()->back()->with('success', 'Caisse supprimée avec succès.');
    }

   
    // Affiche la liste des réceptions
    public function indexreception()
    {
        // $receptions = Reception::with('lignes')->get();
        $receptions = Reception::all();
        $produits = Produit::all();
        $magasins = Magasin::all();
        $ligneCommandes = LigneCommande::select('idP', 'qteCmd', 'prix')->get();
        return view('pages.Approvisionnement.gestion_receptions', compact('receptions', 'produits', 'magasins', 'ligneCommandes'));
    }

    // Ajoute une nouvelle réception
    public function storereception(Request $request)
    {
        $validated = $request->validate([
            'numReception' => 'required|string|max:50',
            'dateReception' => 'required|date',
            'RefNumBordReception' => 'nullable|string',
            'lignes.*.idP' => 'required|exists:produits,idP',
            'lignes.*.idMagasin' => 'required|exists:magasins,idMgs',
            'lignes.*.qteLivr' => 'required|integer|min:1',
            'lignes.*.qteRestant' => 'required|numeric|min:0',
            'lignes.*.prixUn' => 'required|numeric|min:0',
        ]);
    
        // Vérification personnalisée : la quantité livrée ne doit pas dépasser la quantité restante
        foreach ($validated['lignes'] as $ligne) {
            if ($ligne['qteLivr'] > $ligne['qteRestant']) {
                return redirect()->back()->withErrors([
                    'lignes' => "La quantité livrée pour le produit ID {$ligne['idP']} dépasse la quantité restante à livrer.",
                ])->withInput();
            }
        }
    
        // Création de la réception
        $reception = Reception::create([
            'numReception' => $validated['numReception'],
            'dateReception' => $validated['dateReception'],
            'RefNumBordReception' => $request->RefNumBordReception,
        ]);
    
        // Enregistrement des lignes de réception
        foreach ($validated['lignes'] as $ligne) {
            LigneReception::create([
                'idReception' => $reception->idReception,
                'idP' => $ligne['idP'],
                'idMagasin' => $ligne['idMagasin'],
                'qteLivr' => $ligne['qteLivr'],
                'qteRestant' => $ligne['qteRestant'] - $ligne['qteLivr'], // Mise à jour de la quantité restante
                'prixUn' => $ligne['prixUn'],
            ]);
        }
    
        return redirect()->back()->with('success', 'Réception ajoutée avec succès.');
    }
    

    // Modifie une réception existante
    public function updatereception(Request $request, $idReception)
    {
        $validated = $request->validate([
            'numReception' => 'required|string|max:50',
            'dateReception' => 'required|date',
            'lignes.*.idP' => 'required|exists:produits,idP',
            'lignes.*.qteLivr' => 'required|integer|min:1',
            'lignes.*.prixUn' => 'required|numeric|min:0',
            'lignes.*.qteLivr' => 'required|integer|min:1',
            'lignes.*.prixUn' => 'required|numeric|min:0',
            'lignes.*.qteRestant' => 'required|numeric|min:0',
            'lignes.*.prixUn' => 'required|numeric|min:0',
        ]);

        $reception = Reception::findOrFail($idReception);
        $reception->update([
            'numReception' => $validated['numReception'],
            'dateReception' => $validated['dateReception'],
            'RefNumBordReception' => $request->RefNumBordReception,
        ]);

        $reception->lignes()->delete(); // Supprime les anciennes lignes
        foreach ($validated['lignes'] as $ligne) {
            LigneReception::create([
                'idReception' => $reception->idReception,
                'idP' => $ligne['idP'],
                'qteLivr' => $ligne['qteLivr'],
                'prixUn' => $ligne['prixUn'],
                'qteRestant' => $ligne['qteRestant'],
                'prixUn' => $ligne['prixUn'],
            ]);
        }

        return redirect()->back()->with('success', 'Réception mise à jour avec succès.');
    }

    // Supprime une réception
    public function destroyreception($idReception)
    {
        $reception = Reception::findOrFail($idReception);
        $reception->delete();

        return redirect()->back()->with('success', 'Réception supprimée avec succès.');
    }
    

    public function storeCmd(Request $request)
    {
        // Création de la commande
        $commande = new Commande();
        $commande->numCmd = $request->input('numCmd');
        $commande->dateOperation = $request->input('dateOperation');
        $commande->dateRemise = $request->input('dateRemise');
        $commande->descCmd = $request->input('descCmd');
        $commande->delai = $request->input('delai');
        $commande->idF = $request->input('identitefr');
        $commande->save();
    
        // Ajout des lignes de commande
        $lignes = $request->input('lignes', []);
        $montantHT = 0; // Initialisation du montant total HT
        $montantTTC = 0; // Initialisation du montant total TTC
    
        foreach ($lignes as $ligne) {
            $tva = '';
            // Calcul du montant HT et TTC pour chaque ligne
            $prixHT = $ligne['montantht'];
            $tva = $ligne['tva'];
            $qte = $ligne['qte'];
    
            // Calcul du montant total HT et TTC pour la ligne
            $montantLigneHT = $prixHT * $qte;
            $montantLigneTTC = $montantLigneHT + ($montantLigneHT * $tva / 100);
    
            // Mise à jour des totaux
            $montantHT += $montantLigneHT;
            $montantTTC += $montantLigneTTC;
    
            // Création de la ligne de commande
            LigneCommande::create([
                'idCmd' => $commande->idCmd,
                'idP' => $ligne['idP'],
                'prix' => $prixHT,
                'TVA' => $tva,
                'qteCmd' => $qte,
            ]);
        }
    
        // Mise à jour des montants HT et TTC dans la commande
        $commande->montantHT = $montantHT;
        $commande->montantTTC = $montantTTC;
        $commande->save();
    
        return back()->with('status', 'Commande ajoutée avec succès.');
    }
    
    public function updateCmd(Request $request, $idCmd)
    {
        $commande = Commande::find($idCmd);
        
        $commande->numCmd = $request->input('numCmd');
        $commande->dateOperation = $request->input('dateOperation');
        $commande->dateRemise = $request->input('dateRemise');
        $commande->descCmd = $request->input('descCmd');
        $commande->delai = $request->input('delai');
        $commande->idF = $request->input('identitefr');
        $commande->save();
    
        $lignes = $request->input('lignes', []);
        
        foreach ($lignes as $ligne) {
            if (!empty($ligne['idLCmd'])) { 
                // Vérifier si la ligne de commande existe vraiment
                $ligneCommande = LigneCommande::where('idLCmd', $ligne['idLCmd'])
                                              ->where('idCmd', $commande->idCmd)
                                              ->first();
                
                if ($ligneCommande) {
                    // Mise à jour de la ligne existante
                    $ligneCommande->prix = $ligne['montantht'];
                    $ligneCommande->TVA = $ligne['tva'];
                    $ligneCommande->qteCmd = $ligne['qte'];
                    $ligneCommande->save();
                } else {
                    // Éviter les créations inutiles
                    continue;
                }
            } else {
                // Créer une nouvelle ligne de commande UNIQUEMENT si elle n'existe pas déjà
                $existingLine = LigneCommande::where('idCmd', $commande->idCmd)
                                             ->where('idP', $ligne['idP'])
                                             ->first();
                if (!$existingLine) {
                    LigneCommande::create([
                        'idCmd' => $commande->idCmd,
                        'idP' => $ligne['idP'],
                        'prix' => $ligne['montantht'],
                        'TVA' => $ligne['tva'],
                        'qteCmd' => $ligne['qte'],
                    ]);
                }
            }
        }
    
        // Recalcul des montants HT et TTC
        $montantHT = LigneCommande::where('idCmd', $commande->idCmd)
                                   ->sum(DB::raw('prix * qteCmd'));
        $montantTTC = $montantHT + LigneCommande::where('idCmd', $commande->idCmd)
                                                ->sum(DB::raw('prix * qteCmd * TVA / 100'));
    
        $commande->montantHT = $montantHT;
        $commande->montantTTC = $montantTTC;
        $commande->save();
    
        return back()->with('status', 'Commande modifiée avec succès.');
    }

    // Supprime une réception
    public function destroyCommande($idCmd)
    {
        $commande = Commande::findOrFail($idCmd);
        LigneCommande::where('idCmd', $commande->idCmd)->delete();
        $commande->delete();
        return redirect()->back()->with('success', 'Commande supprimée avec succès.');
    }
    
    public function deleteLigneCommande($id)
{
    $ligne = LigneCommande::find($id);
    if ($ligne) {
        $ligne->delete();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false]);
}

}