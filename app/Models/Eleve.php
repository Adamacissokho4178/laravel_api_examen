<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    use HasFactory;

    protected $table = 'eleves';
    protected $fillable = [
        'prenom', 'nom', 'email', 'date_naissance', 'classe_id', 'chemin_document', 'utilisateur_id','identifiant'
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

    // Relation avec les parents
    public function parents()
    {
        return $this->belongsToMany(ParentModel::class, 'parent_eleve', 'eleve_id', 'parent_id')
                    ->withPivot('relation', 'est_principal')
                    ->withTimestamps();
    }

    // Méthode pour obtenir le parent principal
    public function parentPrincipal()
    {
        return $this->parents()->wherePivot('est_principal', true)->first();
    }
} 