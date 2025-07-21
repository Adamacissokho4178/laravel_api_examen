<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index()
    {
        return Categorie::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        return Categorie::create($request->all());
    }

    public function show(Categorie $category)
    {
        return $category;
    }

    public function update(Request $request, Categorie $category)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $category->update($request->all());
        return $category;
    }

    public function destroy(Categorie $category)
    {
        $category->delete();
        return response()->json(['message' => 'Supprimé avec succès']);
    }

}
