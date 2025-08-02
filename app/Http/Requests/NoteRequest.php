<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NoteRequest extends FormRequest
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
            'eleve_id' => 'required|integer|exists:eleves,id',
            'matiere_id' => 'required|integer|exists:matieres,id',
            'enseignant_id' => 'required|integer|exists:enseignants,id',
            'note' => 'required|numeric|min:0|max:20',
            'periode' => 'required|string|in:trimestre1,trimestre2,trimestre3,semestre1,semestre2,annuel',
            'appreciation' => 'nullable|string|max:500',
            'date_evaluation' => 'required|date|before_or_equal:today',
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
            'eleve_id.required' => 'L\'élève est obligatoire.',
            'eleve_id.integer' => 'L\'ID de l\'élève doit être un nombre entier.',
            'eleve_id.exists' => 'L\'élève sélectionné n\'existe pas.',
            
            'matiere_id.required' => 'La matière est obligatoire.',
            'matiere_id.integer' => 'L\'ID de la matière doit être un nombre entier.',
            'matiere_id.exists' => 'La matière sélectionnée n\'existe pas.',
            
            'enseignant_id.required' => 'L\'enseignant est obligatoire.',
            'enseignant_id.integer' => 'L\'ID de l\'enseignant doit être un nombre entier.',
            'enseignant_id.exists' => 'L\'enseignant sélectionné n\'existe pas.',
            
            'note.required' => 'La note est obligatoire.',
            'note.numeric' => 'La note doit être un nombre.',
            'note.min' => 'La note ne peut pas être inférieure à 0.',
            'note.max' => 'La note ne peut pas dépasser 20.',
            
            'periode.required' => 'La période est obligatoire.',
            'periode.string' => 'La période doit être une chaîne de caractères.',
            'periode.in' => 'La période doit être : trimestre1, trimestre2, trimestre3, semestre1, semestre2, ou annuel.',
            
            'appreciation.string' => 'L\'appréciation doit être une chaîne de caractères.',
            'appreciation.max' => 'L\'appréciation ne peut pas dépasser 500 caractères.',
            
            'date_evaluation.required' => 'La date d\'évaluation est obligatoire.',
            'date_evaluation.date' => 'La date d\'évaluation doit être une date valide.',
            'date_evaluation.before_or_equal' => 'La date d\'évaluation ne peut pas être dans le futur.',
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
            'eleve_id' => 'élève',
            'matiere_id' => 'matière',
            'enseignant_id' => 'enseignant',
            'note' => 'note',
            'periode' => 'période',
            'appreciation' => 'appréciation',
            'date_evaluation' => 'date d\'évaluation',
        ];
    }
}
