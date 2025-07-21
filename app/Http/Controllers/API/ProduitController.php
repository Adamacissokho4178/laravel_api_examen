<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProduitRequest;
use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{

    public function index()
    {
        return Produit::with('categorie')->get();
    }


    public function store(ProduitRequest $request)
    {
        return Produit::create($request->all());
    }


    public function show(Produit $produit)
    {
        return $produit;
    }


    public function update(ProduitRequest $request, Produit $produit)
    {
        $produit->update($request->all());
        return $produit;
    }
    

    public function destroy(Produit $produit)
    {
        $produit->delete();
        return response()->json(['message' => 'Supprimé avec succès']);
    }

}
