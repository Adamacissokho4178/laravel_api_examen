<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    public function index()
{
    return response()->json(\App\Models\Matiere::all());
}
}
