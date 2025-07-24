<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $table = 'notes';
    protected $fillable = [
        'eleve_id', 'matiere_id', 'enseignant_id', 'periode', 'note', 'appreciation'
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class, 'enseignant_id');
    }
} 