<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProduitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'libelle' => 'required|string|min:5',
            'prix' => 'required|numeric',
            'desc' => 'nullable|string|min:10|max:1000',
            'idCatPro' => 'nullable|integer',
            'idFamPro' => 'required|integer',
            'idMag' => 'required|integer',
            'stockAlert' => 'required|integer|min:0',
            // 'stockMinimum' => 'required|integer|min:0',
            'stockMinimum'  => 'required|integer|min:0|lt:stockAlert',
            'qteStocke' => 'nullable|integer|min:0',
            'image' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
            'prixReelAchat' => 'nullable|numeric'
        ];

        // Pour la création (quand il n'y a pas d'ID dans la route)
        if (!$this->route('idPro')) {
            $rules['idMag'] = 'required|integer';
            $rules['qteStocke'] = 'required|integer|min:0';
            $rules['image'] = 'required|file|image|mimes:jpg,jpeg,png|max:2048';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.string' => 'Le libellé doit être une chaîne de caractères.',
            'libelle.min' => 'Le libellé doit contenir au moins 5 caractères.',
            
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre valide.',
            
            'desc.required' => 'La description est obligatoire.',
            'desc.string' => 'La description doit être une chaîne de caractères.',
            'desc.min' => 'La description doit contenir au moins 10 caractères.',
            'desc.max' => 'La description ne doit pas dépasser 1000 caractères.',
            
            'idCatPro.integer' => 'La catégorie doit être un entier valide.',
            'idFamPro.integer' => 'La famille doit être un entier valide.',
            'idMag.required' => 'Le magasin est obligatoire.',
            'idMag.integer' => 'Le magasin doit être un entier valide.',
            
            'stockAlert.required' => 'Le seuil d\'alerte est obligatoire.',
            'stockAlert.integer' => 'Le seuil d\'alerte doit être un nombre entier.',
            'stockAlert.min' => 'Le seuil d\'alerte doit être supérieur ou égal à 0.',
            
            'stockMinimum.required' => 'Le stock minimum est obligatoire.',
            'stockMinimum.integer' => 'Le stock minimum doit être un nombre entier.',
            'stockMinimum.min' => 'Le stock minimum doit être supérieur ou égal à 0.',
            'stockMinimum.lt' => 'Le stock minimum doit être strictement inférieur au seuil d’alerte.',
            
            'qteStocke.required' => 'La quantité est obligatoire.',
            'qteStocke.integer' => 'La quantité doit être un nombre entier.',
            'qteStocke.min' => 'La quantité doit être supérieure ou égale à 0.',
            
            'image.required' => 'L\'image est obligatoire pour la création d\'un produit.',
            'image.file' => 'Le fichier doit être un fichier valide.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit avoir l\'une des extensions suivantes : jpg, jpeg, png.',
            'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ];
    }


    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $modalId = request()->route()->getName() === 'modifierProduit'
            ? 'ModifyBoardModal' . request()->route('idPro')
            : 'addBoardModal';

        session()->flash('errorModalId', $modalId);

        parent::failedValidation($validator); // Gardez le comportement par défaut
    }

}
