<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnseignantRequest extends FormRequest
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
        $enseignantId = $this->route('enseignant');
        
        return [
            'nom' => 'required|string|max:255|min:2',
            'prenom' => 'required|string|max:255|min:2',
            'email' => 'required|email|max:255|unique:enseignants,email,' . $enseignantId,
            'specialite' => 'required|string|max:255|min:2',
            'telephone' => 'nullable|string|max:20|regex:/^[0-9+\-\s\(\)]+$/',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'nom.min' => 'Le nom doit contenir au moins 2 caractères.',
            
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'prenom.max' => 'Le prénom ne peut pas dépasser 255 caractères.',
            'prenom.min' => 'Le prénom doit contenir au moins 2 caractères.',
            
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.max' => 'L\'email ne peut pas dépasser 255 caractères.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            
            'specialite.required' => 'La spécialité est obligatoire.',
            'specialite.string' => 'La spécialité doit être une chaîne de caractères.',
            'specialite.max' => 'La spécialité ne peut pas dépasser 255 caractères.',
            'specialite.min' => 'La spécialité doit contenir au moins 2 caractères.',
            
            'telephone.string' => 'Le téléphone doit être une chaîne de caractères.',
            'telephone.max' => 'Le téléphone ne peut pas dépasser 20 caractères.',
            'telephone.regex' => 'Le format du téléphone n\'est pas valide.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom',
            'prenom' => 'prénom',
            'email' => 'email',
            'specialite' => 'spécialité',
            'telephone' => 'téléphone',
        ];
    }
}
