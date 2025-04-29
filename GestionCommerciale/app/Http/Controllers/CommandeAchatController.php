<?php

namespace App\Http\Controllers;

use App\Models\CommandeAchat;
use App\Models\DetailCommandeAchat;
use App\Models\Exercice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommandeAchatController extends Controller
{
    public function index()
    {
        $commandes = CommandeAchat::with(['lignes.produit', 'exercice'])
            ->orderBy('date', 'desc')
            ->get();

        return view('pages.Fournisseur&Achat.gestion_commandes', compact('commandes'));
    }

    public function create()
    {
        $exercices = Exercice::where('statutExercice', 'actif')->get();

        return view('pages.Fournisseur&Achat.commande', compact('exercices'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'date' => 'required|date|before_or_equal:today',
                'reference' => 'required|string|max:255|unique:commande_achats,reference',
                'idExercice' => 'required|exists:exercices,idExercice',
                'lignes.*.idProduit' => 'required|exists:produits,idProduit',
                'lignes.*.qte' => 'required|numeric|min:1',
                'lignes.*.prixUn' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();

            // Création de la commande
            $commande = CommandeAchat::create([
                'date' => $validated['date'],
                'reference' => $validated['reference'],
                'statutCom' => 'validée',
                'idExercice' => $validated['idExercice'],
                'idU' => auth()->id(),
            ]);

            // Création des lignes de commande
            foreach ($validated['lignes'] as $ligne) {
                DetailCommandeAchat::create([
                    'idCommande' => $commande->idCommande,
                    'idProduit' => $ligne['idProduit'],
                    'qte' => $ligne['qte'],
                    'prixUn' => $ligne['prixUn'],
                ]);
            }

            DB::commit();
            return redirect()->route('commandes.index')->with('success', 'Commande créée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création de la commande: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $commande = CommandeAchat::with(['lignes.produit'])->findOrFail($id);
        $exercices = Exercice::where('statutExercice', 'actif')->get();

        return view('pages.Fournisseur&Achat.edit_commande', compact('commande', 'exercices'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $commande = CommandeAchat::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'date' => 'required|date|before_or_equal:today',
                'reference' => 'required|string|max:255|unique:commande_achats,reference,' . $id . ',idCommande',
                'lignes.*.idDetailCom' => 'required|exists:detail_commande_achats,idDetailCom',
                'lignes.*.qte' => 'required|numeric|min:1',
                'lignes.*.prixUn' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();

            // Mise à jour de la commande
            $commande->update([
                'date' => $validated['date'],
                'reference' => $validated['reference'],
            ]);

            // Mise à jour des lignes de commande
            foreach ($validated['lignes'] as $ligne) {
                $detailCommande = DetailCommandeAchat::find($ligne['idDetailCom']);
                $detailCommande->update([
                    'qte' => $ligne['qte'],
                    'prixUn' => $ligne['prixUn'],
                ]);
            }

            DB::commit();
            return redirect()->route('commandes.index')->with('success', 'Commande mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour de la commande: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $commande = CommandeAchat::findOrFail($id);
            $commande->detailCommandeAchat()->delete();
            $commande->delete();

            DB::commit();
            return redirect()->route('commandes.index')->with('success', 'Commande supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression de la commande: ' . $e->getMessage());
        }
    }
}