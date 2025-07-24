<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $fillable = ['nom', 'niveau'];

    public function eleves()
    {
        return $this->hasMany(Eleve::class, 'classe_id');
    }

    public function notes()
    {
        return $this->hasManyThrough(Note::class, Eleve::class, 'classe_id', 'eleve_id');
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'classe_id');
    }
} 