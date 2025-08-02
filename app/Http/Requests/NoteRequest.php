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
            'eleve_id' => 'required|exists:eleves,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'note' => 'required|numeric|min:0|max:20',
            'periode' => 'required|string|in:trimestre1,trimestre2,trimestre3,semestre1,semestre2,annuel',
            'appreciation' => 'nullable|string|max:500',
            'coefficient' => 'nullable|numeric|min:0.1|max:10',
            'type_evaluation' => 'required|string|in:controle,examen,devoir,projet,oral',
            'date_evaluation' => 'required|date|before_or_equal:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'eleve_id.required' => 'L\'élève est obligatoire.',
            'eleve_id.exists' => 'L\'élève sélectionné n\'existe pas.',
            'matiere_id.required' => 'La matière est obligatoire.',
            'matiere_id.exists' => 'La matière sélectionnée n\'existe pas.',
            'enseignant_id.required' => 'L\'enseignant est obligatoire.',
            'enseignant_id.exists' => 'L\'enseignant sélectionné n\'existe pas.',
            'note.required' => 'La note est obligatoire.',
            'note.numeric' => 'La note doit être un nombre.',
            'note.min' => 'La note ne peut pas être négative.',
            'note.max' => 'La note ne peut pas dépasser 20.',
            'periode.required' => 'La période est obligatoire.',
            'periode.in' => 'La période doit être valide (trimestre1, trimestre2, trimestre3, semestre1, semestre2, annuel).',
            'appreciation.max' => 'L\'appréciation ne peut pas dépasser 500 caractères.',
            'coefficient.numeric' => 'Le coefficient doit être un nombre.',
            'coefficient.min' => 'Le coefficient doit être au moins 0.1.',
            'coefficient.max' => 'Le coefficient ne peut pas dépasser 10.',
            'type_evaluation.required' => 'Le type d\'évaluation est obligatoire.',
            'type_evaluation.in' => 'Le type d\'évaluation doit être valide (controle, examen, devoir, projet, oral).',
            'date_evaluation.required' => 'La date d\'évaluation est obligatoire.',
            'date_evaluation.date' => 'La date d\'évaluation doit être une date valide.',
            'date_evaluation.before_or_equal' => 'La date d\'évaluation ne peut pas être dans le futur.',
        ];
    }
}
