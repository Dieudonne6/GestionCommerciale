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
use App\Models\Client;
use App\Models\Vente;
use App\Models\LigneVente;
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
        $request->validate(
            [
                'codeCais' => 'required|string|unique:caises,codeCais|max:255',
                'libelleCais' => 'required|string|max:255',
            ],
            [
                'codeCais.required' => 'Le code de la caisse est obligatoire.',
                'codeCais.unique' => 'Ce code existe déjà. Veuillez en saisir un autre.',
                'libelleCais.required' => 'Le libellé de la caisse est obligatoire.',
            ],
        );

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
        $request->validate(
            [
                'codeCais' => 'required|string|max:255|unique:caises,codeCais,' . $id . ',idCais',
                'libelleCais' => 'required|string|max:255',
            ],
            [
                'codeCais.required' => 'Le code de la caisse est obligatoire.',
                'codeCais.unique' => 'Ce code existe déjà pour une autre caisse.',
                'libelleCais.required' => 'Le libellé de la caisse est obligatoire.',
            ],
        );

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

 // Affichage de la liste des réceptions
 public function indexreception()
 {
     // Récupération des réceptions avec leurs lignes et les produits associés
     $receptions = Reception::with(['lignesReceptions.produit'])->get();
     $produits = Produit::all();
     $magasins = Magasin::all();
     // (Les lignes de réception peuvent être utiles si vous souhaitez les parcourir dans la vue)
     $lignesReceptions = LigneReception::all();

     // Calcul des quantités commandées et déjà livrées pour chaque produit
     $ligneCommandes = LigneCommande::select('idP', 'qteCmd', 'qteLivre', 'prix')
         ->get()
         ->groupBy('idP');

     foreach ($produits as $produit) {
         $commandes = $ligneCommandes[$produit->idP] ?? collect();
         $qteCmdTotale = $commandes->sum('qteCmd');
         $qteLivrTotale = $commandes->sum('qteLivre');
         // On calcule la quantité restante sur la commande
         $produit->qteRestante = $qteCmdTotale - $qteLivrTotale;
         $produit->qteLivreeTotale = $qteLivrTotale;
     }

     return view('pages.Approvisionnement.gestion_receptions', compact('receptions', 'produits', 'magasins', 'lignesReceptions'));
 }

 // Création d'une réception
 public function storereception(Request $request)
 {
     // Validation des données entrantes
     $validated = $request->validate([
         'numReception'         => 'required|string|max:50',
         'dateReception'        => 'required|date',
         'RefNumBonReception'   => 'required|string|max:255',
         'lignes.*.idP'         => 'required|exists:produits,idP',
         'lignes.*.idMagasin'   => 'required|exists:magasins,idMgs',
         'lignes.*.qteLivre'    => 'required|integer|min:1',
     ]);

     // Récupérer l'ensemble des lignes de commande concernées par les produits de la réception
     $produitsIds = array_column($validated['lignes'], 'idP');
     $ligneCommandes = LigneCommande::whereIn('idP', $produitsIds)
         ->get()
         ->groupBy('idP'); // Groupement des lignes de commande par produit

     $idCmd = null;

     // Vérification sur chaque ligne saisie
     foreach ($validated['lignes'] as $index => $ligne) {
         // On récupère la première ligne de commande pour le produit
         $commandeGroup = $ligneCommandes[$ligne['idP']] ?? null;
         if (!$commandeGroup || $commandeGroup->isEmpty()) {
             return redirect()
                 ->back()
                 ->withErrors(["lignes.{$index}.idP" => "Aucune commande trouvée pour le produit ID {$ligne['idP']}."])
                 ->withInput();
         }
         $commande = $commandeGroup->first();

         // Vérifier que tous les produits appartiennent à la même commande
         if ($idCmd === null) {
             $idCmd = $commande->idCmd;
         } elseif ($idCmd !== $commande->idCmd) {
             return redirect()
                 ->back()
                 ->withErrors(["lignes.{$index}.idP" => "Les produits sélectionnés appartiennent à des commandes différentes."])
                 ->withInput();
         }

         // Vérifier que la quantité livrée ne dépasse pas la quantité commandée restante
         $qteCmd = $commande->qteCmd;
         $qteLivreActuelle = $commande->qteLivre;
         if (($qteLivreActuelle + $ligne['qteLivre']) > $qteCmd) {
             return redirect()
                 ->back()
                 ->withErrors(["lignes.{$index}.qteLivre" => "La quantité livrée dépasse la quantité commandée (commande: {$qteCmd}, déjà livrée: {$qteLivreActuelle})."])
                 ->withInput();
         }
     }

     try {
         DB::beginTransaction();

         // Création de la réception
         $reception = Reception::create([
             'numReception'       => $validated['numReception'],
             'dateReception'      => $validated['dateReception'],
             'RefNumBonReception' => $validated['RefNumBonReception'],
             'idCmd'              => $idCmd,
             'idE'                => auth()->id(), // Associe l'utilisateur connecté
         ]);

         // Enregistrement des lignes de réception et mise à jour des lignes de commande associées
         foreach ($validated['lignes'] as $ligne) {
             // Récupération de la première ligne de commande associée au produit
             $commandeGroup = $ligneCommandes[$ligne['idP']];
             $commande = $commandeGroup->first();

             // Création de la ligne de réception
             LigneReception::create([
                 'idReception'   => $reception->idReception,
                 'idP'           => $ligne['idP'],
                 'qteReception'  => $ligne['qteLivre'],
                 'prixUn'        => $commande->prix,
             ]);

             // Mise à jour de la ligne de commande : incrémenter la quantité livrée et recalculer la quantité restante
             $commande->qteLivre += $ligne['qteLivre'];
             $commande->qteRestant = $commande->qteCmd - $commande->qteLivre;
             $commande->save();
         }

         DB::commit();
         return redirect()->route('receptions.index')->with('success', 'Réception enregistrée avec succès.');
     } catch (\Exception $e) {
         DB::rollBack();
         return redirect()->back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement de la réception.'])->withInput();
     }
 }

// Mise à jour d'une réception
public function updatereception(Request $request, $idReception)
{
    // 1. Validation des données du formulaire
    $validated = $request->validate([
        'numReception'         => 'required|string|max:50',
        'dateReception'        => 'required|date',
        'RefNumBonReception'   => 'required|string|max:255',
        'lignes.*.idP'         => 'required|exists:produits,idP',
        'lignes.*.idMagasin'   => 'required|exists:magasins,idMgs',
        'lignes.*.qteLivre'    => 'required|integer|min:1',
        // Vous pouvez ajouter d'autres validations pour qteRestant ou prixUn si besoin
    ]);

    DB::beginTransaction();

    try {
        // 2. Récupération de la réception et de ses lignes existantes
        $reception = Reception::findOrFail($idReception);

        // On indexe les lignes existantes par l'id du produit (idP)
        $lignesReceptionExistantes = $reception->lignesReceptions->keyBy('idP');

        // 3. Récupération des idP transmis et des lignes de commande correspondantes
        $produitsIds = array_column($validated['lignes'], 'idP');

        $ligneCommandes = LigneCommande::whereIn('idP', $produitsIds)
            ->get()
            ->groupBy('idP');

        $idCmd = null;

        // 4. Vérification des quantités pour chaque ligne du formulaire
        foreach ($validated['lignes'] as $index => $ligne) {
            // Vérification que le produit appartient à une commande
            $commandeGroup = $ligneCommandes[$ligne['idP']] ?? null;
            if (!$commandeGroup || $commandeGroup->isEmpty()) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(["lignes.{$index}.idP" => "Aucune commande trouvée pour le produit ID {$ligne['idP']}."])
                    ->withInput();
            }
            $commande = $commandeGroup->first();

            // Vérifier que tous les produits appartiennent à la même commande
            if ($idCmd === null) {
                $idCmd = $commande->idCmd;
            } elseif ($idCmd !== $commande->idCmd) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(["lignes.{$index}.idP" => "Les produits appartiennent à des commandes différentes."])
                    ->withInput();
            }

            // Calcul de la quantité déjà livrée pour ce produit (hors la réception en cours)
            $totalReceptions = LigneReception::whereHas('reception', function ($query) use ($commande) {
                    $query->where('idCmd', $commande->idCmd);
                })
                ->where('idP', $ligne['idP'])
                ->sum('qteReception');

            // Si la ligne existe déjà, on retire sa quantité enregistrée pour cette réception
            $ancienneQteReception = $lignesReceptionExistantes->has($ligne['idP'])
                ? $lignesReceptionExistantes[$ligne['idP']]->qteReception
                : 0;
            $totalReceptions -= $ancienneQteReception;

            // Vérifier que la nouvelle quantité cumulée ne dépasse pas la quantité commandée
            $nouveauTotal = $totalReceptions + $ligne['qteLivre'];
            if ($nouveauTotal > $commande->qteCmd) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(["lignes.{$index}.qteLivre" => "La quantité totale livrée pour le produit ID {$ligne['idP']} dépasse la quantité commandée ({$commande->qteCmd})."])
                    ->withInput();
            }
        }

        // 5. Mise à jour des informations générales de la réception
        $reception->update([
            'numReception'       => $validated['numReception'],
            'dateReception'      => $validated['dateReception'],
            'RefNumBonReception' => $validated['RefNumBonReception'],
            'idCmd'              => $idCmd,
        ]);

        // Récupération des idP envoyés dans le formulaire (pour la suppression ultérieure des lignes manquantes)
        $idPsEnvoyes = array_column($validated['lignes'], 'idP');

        // 6. Mise à jour ou création des lignes de réception et mise à jour de la commande correspondante
        foreach ($validated['lignes'] as $ligne) {
            $commandeGroup = $ligneCommandes[$ligne['idP']];
            $commande = $commandeGroup->first();

            if ($lignesReceptionExistantes->has($ligne['idP'])) {
                // Mise à jour de la ligne de réception existante
                $ligneReception = $lignesReceptionExistantes[$ligne['idP']];
                $ligneReception->update([
                    'qteReception' => $ligne['qteLivre'],
                    // Vous pouvez aussi mettre à jour d'autres champs comme idMagasin si nécessaire
                ]);
            } else {
                // Création d'une nouvelle ligne de réception
                LigneReception::create([
                    'idReception'  => $reception->idReception,
                    'idP'          => $ligne['idP'],
                    'qteReception' => $ligne['qteLivre'],
                    'prixUn'       => $commande->prix, // On récupère le prix depuis la commande
                ]);
            }

            // Recalcul de la quantité totale livrée pour ce produit dans la commande
            $nouvelleQteLivre = LigneReception::whereHas('reception', function ($query) use ($commande) {
                    $query->where('idCmd', $commande->idCmd);
                })
                ->where('idP', $ligne['idP'])
                ->sum('qteReception');

            // Mise à jour de la commande associée (quantité livrée et quantité restante)
            $commande->update([
                'qteLivre'   => $nouvelleQteLivre,
                'qteRestant' => $commande->qteCmd - $nouvelleQteLivre,
            ]);
        }

        // 7. Suppression des lignes de réception qui ne sont plus présentes dans le formulaire
        $lignesASupprimer = $lignesReceptionExistantes->keys()->diff($idPsEnvoyes);
        if ($lignesASupprimer->isNotEmpty()) {
            foreach ($lignesASupprimer as $idP) {
                $ligneSupp = LigneReception::where('idReception', $reception->idReception)
                    ->where('idP', $idP)
                    ->first();
                if ($ligneSupp) {
                    $ligneSupp->delete();

                    // Recalcul de la quantité pour la commande correspondante
                    $commandeGroup = $ligneCommandes[$idP] ?? null;
                    if ($commandeGroup) {
                        $commande = $commandeGroup->first();
                        $nouvelleQteLivre = LigneReception::whereHas('reception', function ($query) use ($commande) {
                                $query->where('idCmd', $commande->idCmd);
                            })
                            ->where('idP', $idP)
                            ->sum('qteReception');
                        $commande->update([
                            'qteLivre'   => $nouvelleQteLivre,
                            'qteRestant' => $commande->qteCmd - $nouvelleQteLivre,
                        ]);
                    }
                }
            }
        }

        DB::commit();
        return redirect()->route('receptions.index')
            ->with('success', 'Réception mise à jour avec succès.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour : ' . $e->getMessage()])
            ->withInput();
    }
}
 

 // Suppression d'une réception
 public function destroyreception($idReception)
 {
     try {
         DB::beginTransaction();
         $reception = Reception::findOrFail($idReception);
         // Suppression des lignes de réception associées
         LigneReception::where('idReception', $idReception)->delete();
         // Suppression de la réception
         $reception->delete();
         DB::commit();
         return redirect()->back()->with('success', 'Réception supprimée avec succès.');
     } catch (\Exception $e) {
         DB::rollBack();
         return redirect()->back()->withErrors(['error' => 'Erreur lors de la suppression de la réception.']);
     }
 }
    public function magasin()
    {
        $allmagasins = Magasin::all();
        return view('pages.definition.magasin', compact('allmagasins'));
    }

    // Création magasin
    public function ajouterMagasin(Request $request)
    {
        // Vérifier si le magasin existe déjà
        $magasinExiste = Magasin::where('codeMgs', $request->input('codeMgs'))->exists();

        if ($magasinExiste) {
            // Retourner une erreur si le magasin existe déjà
            return back()->with(['erreur' => 'Ce magasin existe déjà.']);
        }

        // Créer un nouveau magasin
        Magasin::create([
            'codeMgs' => $request->input('codeMgs'),
            'libelleMgs' => $request->input('libelleMgs'),
        ]);

        return back()->with('status', 'Le magasin a été créé avec succès');
    }

    // Suppression magasin
    public function deleteMagasin($id)
    {
        $magasin = Magasin::find($id); // Utilisation de `find` avec la clé primaire
        if ($magasin) {
            $magasin->delete();
            return back()->with('status', 'Le magasin a été supprimé avec succès');
        }
        return back()->with('erreur', 'Magasin introuvable');
    }

    // Modification magasin
    public function updateMagasin(Request $request, $id)
    {
        $modifMagasin = Magasin::find($id); // Utilisation de `find` avec la clé primaire
        if ($modifMagasin) {
            $modifMagasin->update([
                'codeMgs' => $request->input('codeMgs'),
                'libelleMgs' => $request->input('libelleMgs'),
            ]);

            return back()->with('status', 'Le magasin a été modifié avec succès');
        }
        return back()->with('erreur', 'Magasin introuvable');
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



    public function storeVente(Request $request)
    {
        // Création de la vente
        $vente = new Vente();
        $vente->numV = $request->input('numV');
        $vente->dateOperation = $request->input('dateOperation');
        // $vente->dateRemise = $request->input('dateRemise');
        $vente->descV = $request->input('descV');
        $vente->modePaiement = $request->input('modePaiement');
        $vente->idCl = $request->input('identitefr');
        $vente->save();
    
        // Ajout des lignes de vente
        $lignes = $request->input('lignes', []);
        // $montantHT = 0; // Initialisation du montant total HT
        $montantTTC = 0; // Initialisation du montant total TTC
    
        foreach ($lignes as $ligne) {
            $tva = '';
            // Calcul du montant HT et TTC pour chaque ligne
            $prixTTC = $ligne['montantttc'];
            // $tva = $ligne['tva'];
            $qte = $ligne['qte'];
    
            // Calcul du montant total HT et TTC pour la ligne
            $montantLigneTTC = $prixTTC * $qte;
            // $montantLigneTTC = $montantLigneHT + ($montantLigneHT * $tva / 100);
    
            // Mise à jour des totaux
            // $montantHT += $montantLigneHT;
            $montantTTC += $montantLigneTTC;
    
            // Création de la ligne de commande
            LigneVente::create([
                'idV' => $vente->idV,
                'idP' => $ligne['idP'],
                'prixLVente' => $montantLigneTTC,
                // 'TVA' => $tva,
                'qteLVente' => $qte,
            ]);
        }
    
        // Mise à jour des montants HT et TTC dans la commande
        // $vente->montantHT = $montantHT;
        $vente->montantTTC = $montantTTC;
        $vente->save();
    
        return back()->with('status', 'Vente ajoutée avec succès.');
    }
    
    public function updateVente(Request $request, $idV)
    {
        $vente = Vente::find($idV);
        
        $vente->numV = $request->input('numV');
        $vente->dateOperation = $request->input('dateOperation');
        // $vente->dateRemise = $request->input('dateRemise');
        $vente->descV = $request->input('descV');
        $vente->modePaiement = $request->input('modePaiement');
        $vente->idCl = $request->input('identitefr');
        $vente->save();
    
        $lignes = $request->input('lignes', []);
        
        foreach ($lignes as $ligne) {
            if (!empty($ligne['idLVente'])) { 
                // Vérifier si la ligne de vente existe vraiment
                $ligneVente = LigneVente::where('idLVente', $ligne['idLVente'])
                                              ->where('idV', $vente->idV)
                                              ->first();
                
                if ($ligneVente) {
                    // Mise à jour de la ligne existante
                    $ligneVente->prixLVente = $ligne['montantttc'];
                    // $ligneVente->TVA = $ligne['tva'];
                    $ligneVente->qteLVente = $ligne['qte'];
                    $ligneVente->save();
                } else {
                    // Éviter les créations inutiles
                    continue;
                }
            } else {
                // Créer une nouvelle ligne de commande UNIQUEMENT si elle n'existe pas déjà
                $existingLine = LigneVente::where('idV', $commande->idV)
                                             ->where('idP', $ligne['idP'])
                                             ->first();
                if (!$existingLine) {
                    LigneVente::create([
                        'idV' => $vente->idV,
                        'idP' => $ligne['idP'],
                        'prixLVente' => $ligne['montantttc'],
                        // 'TVA' => $ligne['tva'],
                        'qteLVente' => $ligne['qte'],
                    ]);
                }
            }
        }
    
        // Recalcul des montants HT et TTC
        $montantTTC = LigneVente::where('idV', $vente->idV)
                                   ->sum(DB::raw('prixLVente * qteLVente'));
        // $montantTTC = $montantHT + LigneVente::where('idV', $vente->idCmd)
        //                                         ->sum(DB::raw('prix * qteCmd * TVA / 100'));
    
        // $vente->montantHT = $montantHT;
        $vente->montantTTC = $montantTTC;
        $vente->save();
    
        return back()->with('status', 'Vente modifiée avec succès.');
    }

    // Supprime une réception
    public function destroyVente($idV)
    {
        $vente = Vente::findOrFail($idV);
        LigneVente::where('idV', $vente->idV)->delete();
        $vente->delete();
        return redirect()->back()->with('success', 'Vente supprimée avec succès.');
    }
    
    public function deleteLigneVente($id)
{
    $ligne = LigneVente::find($id);
    if ($ligne) {
        $ligne->delete();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false]);
}

}