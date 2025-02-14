<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategorieProduitRequest extends FormRequest
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
            'codeCatPro' => 'required|string|min:5',
            'libelle' => 'required|string|min:5',
        ];
    }

    public function messages() {
        return[
            'codeCatPro.required' => 'Le code Categorie est obligatoire.',
            'codeCatPro.string' => 'Le code Categorie doit être une chaîne de caractères.',
            'codeCatPro.min' => 'Le code Categorie doit avoir au minimum 5 caractères.',
            'libelle.required' => 'Le libelle est obligatoire.',
            'libelle.string' => 'Le libelle doit être une chaîne de caractères.',
            'libelle.min' => 'Le libelle doit avoir au minimum 5 caractères.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $modalId = request()->route()->getName() === 'modifierCategorieProduit'
            ? 'ModifyBoardModal' . request()->route('idCatPro')
            : 'addBoardModal';
    
        session()->flash('errorModalId', $modalId);
    
        parent::failedValidation($validator); // Gardez le comportement par défaut
    }
}
