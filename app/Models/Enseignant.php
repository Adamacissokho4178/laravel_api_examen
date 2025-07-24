<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enseignant extends Model
{
    use HasFactory;

    protected $table = 'enseignants';
    protected $fillable = ['utilisateur_id', 'specialite'];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'enseignant_id');
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'enseignant_id');
    }
} 