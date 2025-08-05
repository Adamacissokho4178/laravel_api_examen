<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'utilisateur_id',
        'identifiant'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    // Relation avec l'utilisateur
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    // Relation avec les enfants (élèves)
    public function enfants()
    {
        return $this->belongsToMany(Eleve::class, 'parent_eleve', 'parent_id', 'eleve_id')
                    ->withTimestamps();
    }

    // Méthode pour vérifier si un parent a accès à un élève spécifique
    public function peutAccederA($eleveId)
    {
        return $this->enfants()->where('eleve_id', $eleveId)->exists();
    }

    // Méthode pour obtenir les identifiants des enfants
    public function getIdentifiantsEnfants()
    {
        return $this->enfants()->pluck('identifiant')->toArray();
    }
} 