<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class FamilleProduitRequest extends FormRequest
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

        $idFamPro = $this->route('idFamPro');

        return [
            'codeFamille' => [
                'required',
                'string',
                'min:5',
                Rule::unique('famille_produits', 'codeFamille')
                    ->when($idFamPro, fn ($rule) => $rule->ignore($idFamPro, 'idFamPro')),
            ],

            'libelle' => [
                'required',
                'string',
                'min:5',
                Rule::unique('famille_produits', 'libelle')
                    ->when($idFamPro, fn ($rule) => $rule->ignore($idFamPro, 'idFamPro')),
            ],

            'TVA' => 'nullable',
            'coeff' => 'nullable',
        ];
        


    }

    public function messages() {
        return[
            'codeFamille.required' => 'Le code Famille est obligatoire.',
            'codeFamille.string' => 'Le code Famille doit être une chaîne de caractères.',
            'codeFamille.min' => 'Le code Famille doit avoir au minimum 5 caractères.',
            'codeFamille.unique' => 'Ce code de Famille existe déjà.',
            'libelle.required' => 'Le libelle est obligatoire.',
            'libelle.string' => 'Le libelle doit être une chaîne de caractères.',
            'libelle.min' => 'Le libelle doit avoir au minimum 5 caractères.',
            'libelle.unique' => 'Ce libelle existe déjà.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $modalId = request()->route()->getName() === 'modifierFamilleProduit'
            ? 'ModifyBoardModal' . request()->route('idFamPro')
            : 'addBoardModal';
    
        session()->flash('errorModalId', $modalId);
    
        parent::failedValidation($validator); // Gardez le comportement par défaut
    }
}
