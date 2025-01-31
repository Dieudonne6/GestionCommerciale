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
       $receptions = Reception::with(['lignesReceptions.produit'])->get();
       $produits = Produit::all();
       $magasins = Magasin::all();
       $lignesReceptions = LigneReception::all();

       // Récupérer les quantités commandées et livrées
       $ligneCommandes = LigneCommande::select('idP', 'qteCmd', 'qteLivre', 'prix')->get()->groupBy('idP');

       foreach ($produits as $produit) {
           $commandes = $ligneCommandes[$produit->idP] ?? collect();
           $qteCmdTotale = $commandes->sum('qteCmd');
           $qteLivrTotale = $commandes->sum('qteLivre');
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
        'numReception' => 'required|string|max:50',
        'dateReception' => 'required|date',
        'RefNumBonReception' => 'required|string|max:255',
        'lignes.*.idP' => 'required|exists:produits,idP',
        'lignes.*.idMagasin' => 'required|exists:magasins,idMgs',
        'lignes.*.qteLivre' => 'required|integer|min:1',
    ]);

    // Récupération des commandes liées aux produits sélectionnés
    $produitsIds = array_column($validated['lignes'], 'idP');
    $ligneCommandes = LigneCommande::whereIn('idP', $produitsIds)
        ->get()
        ->keyBy('idP'); // Associe chaque ligne de commande au produit correspondant

    // Vérification et récupération de idCmd pour la réception
    $idCmd = null;
    foreach ($validated['lignes'] as $index => $ligne) {
        $commande = $ligneCommandes[$ligne['idP']] ?? null;

        if (!$commande) {
            return redirect()
                ->back()
                ->withErrors(["lignes.{$index}.idP" => "Aucune commande trouvée pour le produit ID {$ligne['idP']}."])
                ->withInput();
        }

        // Vérification de l'unicité de la commande
        if ($idCmd === null) {
            $idCmd = $commande->idCmd;
        } elseif ($idCmd !== $commande->idCmd) {
            return redirect()
                ->back()
                ->withErrors(["lignes.{$index}.idP" => "Les produits sélectionnés appartiennent à des commandes différentes."])
                ->withInput();
        }

        if ($ligne['qteLivre'] > $commande->qteCmd) {
            return redirect()
                ->back()
                ->withErrors(["lignes.{$index}.qteLivre" => "La quantité livrée dépasse la quantité commandée ({$commande->qteCmd})."])
                ->withInput();
        }
    }

    // Création de la réception
    $receptions = Reception::create([
        'numReception' => $validated['numReception'],
        'dateReception' => $validated['dateReception'],
        'RefNumBonReception' => $validated['RefNumBonReception'],
        'idCmd' => $idCmd,
        'idE' => auth()->id(), // Associe à l'utilisateur connecté si applicable
    ]);

    // Enregistrement des lignes de réception
    foreach ($validated['lignes'] as $ligne) {
        $commande = $ligneCommandes[$ligne['idP']];

        LigneReception::create([
            'idReception' => $receptions->idReception,
            'idP' => $ligne['idP'],
            'qteReception' => $ligne['qteLivre'],
            'prixUn' => $commande->prix,
        ]);
    }

    return redirect()->route('receptions.index')->with('success', 'Réception enregistrée avec succès.');
}

   // Modification d'une réception
//    public function updatereception(Request $request, $idReception)
// {
//     // Validation des données
//     $validated = $request->validate([
//         'numReception' => 'required|string|max:50',
//         'dateReception' => 'required|date',
//         'RefNumBonReception' => 'required|string|max:255',
//         'lignes.*.idP' => 'required|exists:produits,idP',
//         'lignes.*.idMagasin' => 'required|exists:magasins,idMgs',
//         'lignes.*.qteLivre' => 'required|integer|min:1',
//     ]);

//     // Récupération des commandes liées aux produits
//     $produitsIds = array_column($validated['lignes'], 'idP');
//     $ligneCommandes = LigneCommande::whereIn('idP', $produitsIds)
//         ->get()
//         ->keyBy('idP');

//     // Vérification et récupération de idCmd
//     $idCmd = null;
//     foreach ($validated['lignes'] as $index => $ligne) {
//         $commande = $ligneCommandes[$ligne['idP']] ?? null;

//         if (!$commande) {
//             return redirect()
//                 ->back()
//                 ->withErrors(["lignes.{$index}.idP" => "Aucune commande trouvée pour le produit ID {$ligne['idP']}."])
//                 ->withInput();
//         }

//         if ($idCmd === null) {
//             $idCmd = $commande->idCmd;
//         } elseif ($idCmd !== $commande->idCmd) {
//             return redirect()
//                 ->back()
//                 ->withErrors(["lignes.{$index}.idP" => "Les produits sélectionnés appartiennent à des commandes différentes."])
//                 ->withInput();
//         }

//         if ($ligne['qteLivre'] > $commande->qteCmd) {
//             return redirect()
//                 ->back()
//                 ->withErrors(["lignes.{$index}.qteLivre" => "La quantité livrée dépasse la quantité commandée ({$commande->qteCmd})."])
//                 ->withInput();
//         }
//     }

//     // Vérification et mise à jour de la réception
//     $reception = Reception::findOrFail($idReception);
//     $reception->update([
//         'numReception' => $validated['numReception'],
//         'dateReception' => $validated['dateReception'],
//         'RefNumBonReception' => $validated['RefNumBonReception'],
//         'idCmd' => $idCmd,
//     ]);

//     // Suppression des anciennes lignes et ajout des nouvelles
//     LigneReception::where('idReception', $reception->idReception)->delete();

//     foreach ($validated['lignes'] as $ligne) {
//         $commande = $ligneCommandes[$ligne['idP']];

//         LigneReception::create([
//             'idReception' => $reception->idReception,
//             'idP' => $ligne['idP'],
//             'qteReception' => $ligne['qteLivre'],
//             'prixUn' => $commande->prix,
//         ]);
//     }

//     return redirect()->route('receptions.index')->with('success', 'Réception mise à jour avec succès.');
// }


public function updatereception(Request $request, $idReception)
{
    // Validation des données
    $validated = $request->validate([
        'numReception' => 'required|string|max:50',
        'dateReception' => 'required|date',
        'RefNumBonReception' => 'required|string|max:255',
        'lignes.*.idP' => 'required|exists:produits,idP',
        'lignes.*.idMagasin' => 'required|exists:magasins,idMgs',
        'lignes.*.qteLivre' => 'required|integer|min:1',
    ]);

    try {
        DB::beginTransaction();

        // Récupération des commandes liées aux produits
        $produitsIds = array_column($validated['lignes'], 'idP');
        $ligneCommandes = LigneCommande::whereIn('idP', $produitsIds)
            ->get()
            ->keyBy('idP');

        // Vérification et récupération de idCmd
        $idCmd = null;
        foreach ($validated['lignes'] as $index => $ligne) {
            $commande = $ligneCommandes[$ligne['idP']] ?? null;

            if (!$commande) {
                return redirect()
                    ->back()
                    ->withErrors(["lignes.{$index}.idP" => "Aucune commande trouvée pour le produit ID {$ligne['idP']}."])
                    ->withInput();
            }

            if ($idCmd === null) {
                $idCmd = $commande->idCmd;
            } elseif ($idCmd !== $commande->idCmd) {
                return redirect()
                    ->back()
                    ->withErrors(["lignes.{$index}.idP" => "Les produits appartiennent à des commandes différentes."])
                    ->withInput();
            }

            if ($ligne['qteLivre'] > $commande->qteCmd) {
                return redirect()
                    ->back()
                    ->withErrors(["lignes.{$index}.qteLivre" => "Quantité livrée supérieure à la commande ({$commande->qteCmd})."])
                    ->withInput();
            }
        }

        // Mise à jour de la réception
        $receptions = Reception::findOrFail($idReception);
        $receptions->update([
            'numReception' => $validated['numReception'],
            'dateReception' => $validated['dateReception'],
            'RefNumBonReception' => $validated['RefNumBonReception'],
            'idCmd' => $idCmd,
        ]);

        // Mise à jour des lignes de réception
        $existingLignes = LigneReception::where('idReception', $receptions->idReception)->pluck('idP')->toArray();

        foreach ($validated['lignes'] as $ligne) {
            if (in_array($ligne['idP'], $existingLignes)) {
                // Mise à jour de la ligne existante
                LigneReception::where([
                    'idReception' => $receptions->idReception,
                    'idP' => $ligne['idP'],
                ])->update(['qteReception' => $ligne['qteLivre']]);
            } else {
                // Création d'une nouvelle ligne
                LigneReception::create([
                    'idReception' => $receptions->idReception,
                    'idP' => $ligne['idP'],
                    'qteReception' => $ligne['qteLivre'],
                    'prixUn' => $ligneCommandes[$ligne['idP']]->prix,
                ]);
            }
        }

        DB::commit();
        return redirect()->route('receptions.index')->with('success', 'Réception mise à jour avec succès.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour.'])->withInput();
    }
}


   // Suppression d'une réception
   public function destroyreception($idReception)
   {
       $receptions = Reception::findOrFail($idReception);
       LigneReception::where('idReception', $idReception)->delete();
       $receptions->delete();

       return redirect()->back()->with('success', 'Réception supprimée avec succès.');
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
}