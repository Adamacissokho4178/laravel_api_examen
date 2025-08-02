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
            'nom' => 'required|string|max:255|min:2',
            'niveau' => 'required|string|in:6ème,5ème,4ème,3ème,2nde,1ère,Terminale',
            'coefficient' => 'required|numeric|min:0.1|max:10',
            'description' => 'nullable|string|max:1000',
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
            'nom.required' => 'Le nom de la matière est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'nom.min' => 'Le nom doit contenir au moins 2 caractères.',
            
            'niveau.required' => 'Le niveau est obligatoire.',
            'niveau.string' => 'Le niveau doit être une chaîne de caractères.',
            'niveau.in' => 'Le niveau doit être : 6ème, 5ème, 4ème, 3ème, 2nde, 1ère, ou Terminale.',
            
            'coefficient.required' => 'Le coefficient est obligatoire.',
            'coefficient.numeric' => 'Le coefficient doit être un nombre.',
            'coefficient.min' => 'Le coefficient doit être au moins 0.1.',
            'coefficient.max' => 'Le coefficient ne peut pas dépasser 10.',
            
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
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
            'nom' => 'nom de la matière',
            'niveau' => 'niveau',
            'coefficient' => 'coefficient',
            'description' => 'description',
        ];
    }
}
