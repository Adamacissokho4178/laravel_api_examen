<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    use HasFactory;

    protected $table = 'matieres';
    protected $fillable = ['nom', 'niveau', 'coefficient'];

    public function notes()
    {
        return $this->hasMany(Note::class, 'matiere_id');
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'matiere_id');
    }
} 