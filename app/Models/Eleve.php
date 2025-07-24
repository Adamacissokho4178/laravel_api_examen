<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    use HasFactory;

    protected $table = 'eleves';
    protected $fillable = [
        'prenom', 'nom', 'email', 'date_naissance', 'classe_id', 'chemin_document', 'utilisateur_id'
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'eleve_id');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
} 