<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntrepriseRequest extends FormRequest
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
            'nom' => 'required|string|min:3',
            'IFU' => 'required|digits:13|numeric|unique:entreprises,IFU',
            'adresse' => 'required|string',
            'telephone' => 'required|string',
            'mail' => 'required|string',
            'RCCM' => 'required|string',
            'regime' => 'required|string',
            'logo' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',  // Extension jpg, jpeg, png et taille max 2 Mo
            'idParent' => 'nullable|exists:entreprises,idE', // Permet d'accepter null ou une valeur existante
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.min' => 'Le nom doit contenir au moins 5 caractères.',
            
            'IFU.required' => 'Le numero IFU est obligatoire.',
            // 'IFU.numeric' => 'Le numero IFU doit être un nombre valide.',
            'IFU.numeric'  => 'L\'IFU doit être numérique.',
            'IFU.unique'   => 'Cet IFU existe déjà.',
            
            'telephone.required' => 'Le numero de telephone est obligatoire.',
            'telephone.numeric' => 'Le numero de telephone doit être un nombre valide.',
            
            'adresse.required' => 'L\'adresse est obligatoire.',
            'adresse.string' => 'L\'adresse  doit être une chaîne de caractères.',
            
            'mail.required' => 'Le mail est obligatoire.',
            'mail.string' => 'Le mail  doit être une chaîne de caractères.',
            
            'RCCM.required' => 'Le RCCM est obligatoire.',
            'RCCM.string' => 'Le RCCM  doit être une chaîne de caractères.',
            
            'regime.required' => 'Le regime est obligatoire.',
            'regime.string' => 'Le regime  doit être une chaîne de caractères.',
            
            'logo.required' => 'Le logo est obligatoire.',
            'logo.file' => 'Le fichier doit être un fichier valide.',
            'logo.image' => 'Le fichier doit être une image.',
            'logo.mimes' => 'Le logo doit avoir l\'une des extensions suivantes : jpg, jpeg, png.',
            'logo.max' => 'Le logo ne doit pas dépasser 2 Mo.',
            
            'idParent.string' => 'L\'idParent doit être une chaîne de caractères.',
        ];
    }

    
protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
{
    $modalId = request()->route()->getName() === 'modifEntreprise'
        ? 'ModifyBoardModal' . request()->route('idE')
        : 'addBoardModal';

    session()->flash('errorModalId', $modalId);

    parent::failedValidation($validator); // Gardez le comportement par défaut
}
}
