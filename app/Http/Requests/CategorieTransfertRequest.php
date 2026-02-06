<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategorieTransfertRequest extends FormRequest
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

        // Récupère l'id depuis la route (null en création)
        $id = $this->route('id');

        return [
            'code' => [
                'required',
                'string',
                'min:3',
                Rule::unique('categories_tarifaires', 'code')->ignore($id),

            ],


            'libelle' => [
                'required',
                'string',
                'min:3',
                Rule::unique('categories_tarifaires', 'libelle')->ignore($id),

            ],

            'type_reduction' => [
                'required',
                'string'
                // Rule::unique('categories_tarifaires', 'type_reduction')
                //     ->when($id, fn ($rule) => $rule->ignore($id, 'id')),
            ],

            'valeur_reduction' => [
                'required',
                'string'
            ],

            'aib' => [
                'required',
                'string'
            ],
        ];
    }

        public function messages() {
            return[
                'code.required' => 'Le code est obligatoire.',
                'code.string' => 'Le code doit être une chaîne de caractères.',
                'code.min' => 'Le code doit avoir au minimum 3 caractères.',
                'code.unique' => 'Ce code existe déjà.',
                'libelle.required' => 'Le libelle est obligatoire.',
                'libelle.string' => 'Le libelle doit être une chaîne de caractères.',
                'libelle.min' => 'Le libelle doit avoir au minimum 3 caractères.',
                'libelle.unique' => 'Ce libelle existe déjà.',
                'type_reduction.required' => 'Le type de la reduction est obligatoire.',
                'type_reduction.string' => 'Le type de la reduction doit être une chaîne de caractères.',
                'valeur_reduction.required' => 'La valeur de la reduction est obligatoire.',
                'valeur_reduction.string' => 'La valeur de la reduction doit être une chaîne de caractères.',
                'aib.required' => 'La valeur de l\'aib est obligatoire.',
                'aib.string' => 'La valeur de l\'aib doit être une chaîne de caractères.',
            ];
    }


    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $modalId = request()->route()->getName() === 'EditCategorieTarifaire'
            ? 'ModifyBoardModal' . request()->route('id')
            : 'addBoardModal';

        session()->flash('errorModalId', $modalId);

        parent::failedValidation($validator); // Gardez le comportement par défaut
    }
}
