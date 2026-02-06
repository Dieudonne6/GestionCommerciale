<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategorieTarifaire;
use App\Http\Requests\CategorieTransfertRequest;


class CategorieTarifaireController extends Controller
{
    // Affiche la liste des categories tarifaire
    public function CategorieTarifaire()
    {
        $categorietarifaires = CategorieTarifaire::get();
        // dd($Entreprises);
        return view('pages.parametres.categorie_tarifaire', compact('categorietarifaires'));
    }

    // Ajoute une nouvelle categorie tarifaire
    public function CreateCategorieTarifaire(CategorieTransfertRequest $request)
    {
        $request->validated();
        try {
            $categorieTarifaire = new CategorieTarifaire();
            $categorieTarifaire->code = $request->input('code');
            $categorieTarifaire->libelle = $request->input('libelle');
            $categorieTarifaire->type_reduction = $request->input('type_reduction');
            $categorieTarifaire->valeur_reduction = $request->input('valeur_reduction');
            $categorieTarifaire->aib = $request->input('aib');
            $categorieTarifaire->save();

            return back()->with("status", "La categorie Tarifaire a été créée avec succès.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->with('errorModalId', 'addBoardModal'); // ID du modal d'ajout
        }
    }

    // activer ou desactivé une categorie tarifaire
    public function ActiverouDeasactiverCategorieTarifaire($id)
    {
        try {
            $categorieTarif = CategorieTarifaire::findOrFail($id);

            // toggle actif / inactif
            $categorieTarif->actif = !$categorieTarif->actif;
            $categorieTarif->save();

            return back()->with(
                "status",
                $categorieTarif->actif
                    ? "La catégorie tarifaire a été activée avec succès."
                    : "La catégorie tarifaire a été désactivée avec succès."
            );

        } catch (\Exception $e) {
            return back()->with(
                'erreur',
                "Une erreur est survenue lors de l'activation/désactivation."
            );
        }
    }


    // Modifie une categorie tarifaire existante
    public function EditCategorieTarifaire(CategorieTransfertRequest $request, $id)
    {
        $request->validated();


        try {
            $modifcategorieTarifaire = CategorieTarifaire::where('id', $id)->firstOrFail();
            $modifcategorieTarifaire->code = $request->input('code');
            $modifcategorieTarifaire->libelle = $request->input('libelle');
            $modifcategorieTarifaire->type_reduction = $request->input('type_reduction');
            $modifcategorieTarifaire->valeur_reduction = $request->input('valeur_reduction');
            $modifcategorieTarifaire->aib = $request->input('aib');
            $modifcategorieTarifaire->save();
            return back()->with("status", "La categorie tarifaire a été modifiée avec succès.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->with('errorModalId', 'ModifyBoardModal' . $id); // Utilisation de $idE pour identifier le modal
        }
    }
}
