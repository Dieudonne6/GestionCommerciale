<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
            'identiteCl' => 'required',
            // 'PrenomCl' => 'required',
            'AdresseCl' => 'required',
            'ContactCl' => 'required',
        ];
    }

    public function messages() {
        return[

            // 'classe.required' => 'La classe de l\'élève est obligatoire.',
            'identiteCl.required' => 'L\'identité est obligatoire.',
            // 'PrenomCl.required' => 'Le prenom est obligatoire.',
            'AdresseCl.required' => 'L\'Adresse est obligatoire.',
            'ContactCl.required' => 'Le contact est obligatoire.',
        ];
    }
}
