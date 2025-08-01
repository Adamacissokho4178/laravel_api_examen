<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notes = Note::with('eleve', 'matiere', 'enseignant')->get();
        return response()->json($notes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'periode' => 'required|string',
            'note' => 'required|numeric|min:0|max:20',
            'appreciation' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $note = Note::create($request->only(['eleve_id', 'matiere_id', 'enseignant_id', 'periode', 'note', 'appreciation']));
        return response()->json($note->load('eleve', 'matiere', 'enseignant'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $note = Note::with('eleve', 'matiere', 'enseignant')->find($id);
        if (!$note) {
            return response()->json(['message' => 'Note non trouvée'], 404);
        }
        return response()->json($note);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $note = Note::find($id);
        if (!$note) {
            return response()->json(['message' => 'Note non trouvée'], 404);
        }
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'sometimes|required|exists:eleves,id',
            'matiere_id' => 'sometimes|required|exists:matieres,id',
            'enseignant_id' => 'sometimes|required|exists:enseignants,id',
            'periode' => 'sometimes|required|string',
            'note' => 'sometimes|required|numeric|min:0|max:20',
            'appreciation' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $note->update($request->only(['eleve_id', 'matiere_id', 'enseignant_id', 'periode', 'note', 'appreciation']));
        return response()->json($note->load('eleve', 'matiere', 'enseignant'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $note = Note::find($id);
        if (!$note) {
            return response()->json(['message' => 'Note non trouvée'], 404);
        }
        $note->delete();
        return response()->json(['message' => 'Note supprimée']);
    }
}
