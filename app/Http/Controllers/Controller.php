<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Caise;
use App\Models\Reception;
use App\Models\Commande; 
use App\Models\Magasin; 
use Illuminate\Http\Request;


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

    public function reception()
    {
        // Récupérer les commandes et magasins
        $commandes = Commande::all(); // Vous pouvez ajuster cette requête si nécessaire
        $magasins = Magasin::all();  // Récupérer tous les magasins
        
        return view('pages.Approvisionnement.reception', compact('commandes', 'magasins'));
    }

    public function handleReception(Request $request)
    {
        $commandeId = $request->input('typeC');
        $dateReception = $request->input('dateC');
        $referenceBL = $request->input('referenceC');
        $magasinId = $request->input('magasin');
    
        // Récupérer les lignes de commande associées à la commande sélectionnée
        $commande = Commande::find($commandeId);
        $lignesCommandes = $commande->lignesCommandes;
    
        // Traiter chaque ligne de commande
        foreach ($lignesCommandes as $ligne) {
            $quantiteRecue = $request->input('quantite_' . $ligne->id);
            
            // Mettre à jour les stocks dans le magasin
            $stockMagasin = Stock::where('magasin_id', $magasinId)
                                ->where('produit_id', $ligne->produit_id)
                                ->first();
    
            if ($stockMagasin) {
                $stockMagasin->quantite += $quantiteRecue;
                $stockMagasin->save();
            }
    
            // Enregistrer la réception
            Reception::create([
                'commande_id' => $commandeId,
                'produit_id' => $ligne->produit_id,
                'quantite_recue' => $quantiteRecue,
                'date_reception' => $dateReception,
                'reference_bl' => $referenceBL,
                'magasin_id' => $magasinId,
            ]);
        }
    
        return redirect()->route('reception')->with('success', 'Réception enregistrée avec succès');
    }
    

}