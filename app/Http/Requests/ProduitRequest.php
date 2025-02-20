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
        return [
            'libelle' => 'required|string|min:5',
            'prix' => 'required|numeric',
            'desc' => 'required|string|min:10|max:1000',
            'image' => 'required|file|image|mimes:jpg,jpeg,png|max:2048',  // Extension jpg, jpeg, png et taille max 2 Mo
            'stockAlert' => 'nullable|string',
            'stockMinimum' => 'nullable|string',
            'idCatPro' => 'nullable|string',
            'idFamPro' => 'nullable|string',
        ];
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
        
        'image.required' => 'L\'image est obligatoire.',
        'image.file' => 'Le fichier doit être un fichier valide.',
        'image.image' => 'Le fichier doit être une image.',
        'image.mimes' => 'L\'image doit avoir l\'une des extensions suivantes : jpg, jpeg, png.',
        'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        
        'stockAlert.string' => 'L\'alerte de stock doit être une chaîne de caractères.',
        'stockMinimum.string' => 'Le stock minimum doit être une chaîne de caractères.',
        'idCatPro.string' => 'L\'ID de la catégorie produit doit être une chaîne de caractères.',
        'idFamPro.string' => 'L\'ID de la famille produit doit être une chaîne de caractères.',
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
