<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MatiereRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autoriser l'accès
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'niveau' => 'required|string|max:255',
            'coefficient' => 'required|numeric|min:0.1|max:10',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de la matière est obligatoire.',
            'nom.max' => 'Le nom de la matière ne peut pas dépasser 255 caractères.',
            'niveau.required' => 'Le niveau est obligatoire.',
            'niveau.max' => 'Le niveau ne peut pas dépasser 255 caractères.',
            'coefficient.required' => 'Le coefficient est obligatoire.',
            'coefficient.numeric' => 'Le coefficient doit être un nombre.',
            'coefficient.min' => 'Le coefficient doit être au moins 0.1.',
            'coefficient.max' => 'Le coefficient ne peut pas dépasser 10.',
        ];
    }
}
