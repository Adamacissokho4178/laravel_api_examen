<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur admin par défaut
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@ecole.fr',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Créer un utilisateur enseignant par défaut
        User::create([
            'name' => 'Jean Dupont',
            'email' => 'enseignant@ecole.fr',
            'password' => Hash::make('enseignant123'),
            'role' => 'enseignant',
        ]);

        // Créer un utilisateur élève/parent par défaut
        User::create([
            'name' => 'Marie Martin',
            'email' => 'eleve@ecole.fr',
            'password' => Hash::make('eleve123'),
            'role' => 'eleve_parent',
        ]);
    }
}
