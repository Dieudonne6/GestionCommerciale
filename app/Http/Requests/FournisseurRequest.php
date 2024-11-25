<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FournisseurRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     * 
     * 
     */
    public function rules(): array
    {
        return [
            'NomF' => 'required',
            'PrenomF' => 'required',
            'AdresseF' => 'required',
            'ContactF' => 'required',
        ];
    }

    public function messages() {
        return[

            // 'classe.required' => 'La classe de l\'élève est obligatoire.',
            'NomF.required' => 'Le nom est obligatoire.',
            'PrenomF.required' => 'Le prenom est obligatoire.',
            'AdresseF.required' => 'L\'Adresse est obligatoire.',
            'ContactF.required' => 'Le contact est obligatoire.',
        ];
    }
}
