<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Matiere;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des utilisateurs de test
        $users = [
            [
                'name' => 'Admin Principal',
                'email' => 'admin@ecole.com',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ],
            [
                'name' => 'Jean Dupont',
                'email' => 'jean.dupont@ecole.com',
                'password' => Hash::make('password123'),
                'role' => 'enseignant'
            ],
            [
                'name' => 'Marie Martin',
                'email' => 'marie.martin@ecole.com',
                'password' => Hash::make('password123'),
                'role' => 'enseignant'
            ],
            [
                'name' => 'Pierre Durand',
                'email' => 'pierre.durand@ecole.com',
                'password' => Hash::make('password123'),
                'role' => 'eleve'
            ],
            [
                'name' => 'Sophie Parent',
                'email' => 'sophie.parent@ecole.com',
                'password' => Hash::make('password123'),
                'role' => 'parent'
            ]
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Créer des classes de test
        $classes = [
            [
                'nom' => '6ème A',
                'niveau' => '6ème',
                'annee_scolaire' => '2024-2025',
                'effectif' => 25
            ],
            [
                'nom' => '6ème B',
                'niveau' => '6ème',
                'annee_scolaire' => '2024-2025',
                'effectif' => 28
            ],
            [
                'nom' => '5ème A',
                'niveau' => '5ème',
                'annee_scolaire' => '2024-2025',
                'effectif' => 26
            ],
            [
                'nom' => '4ème A',
                'niveau' => '4ème',
                'annee_scolaire' => '2024-2025',
                'effectif' => 24
            ],
            [
                'nom' => '3ème A',
                'niveau' => '3ème',
                'annee_scolaire' => '2024-2025',
                'effectif' => 27
            ]
        ];

        foreach ($classes as $classeData) {
            Classe::create($classeData);
        }

        // Créer des enseignants de test
        $enseignants = [
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'email' => 'jean.dupont@ecole.com',
                'specialite' => 'Mathématiques',
                'telephone' => '0123456789'
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Martin',
                'email' => 'marie.martin@ecole.com',
                'specialite' => 'Français',
                'telephone' => '0123456790'
            ],
            [
                'first_name' => 'Pierre',
                'last_name' => 'Bernard',
                'email' => 'pierre.bernard@ecole.com',
                'specialite' => 'Histoire-Géographie',
                'telephone' => '0123456791'
            ],
            [
                'first_name' => 'Sophie',
                'last_name' => 'Leroy',
                'email' => 'sophie.leroy@ecole.com',
                'specialite' => 'Sciences',
                'telephone' => '0123456792'
            ]
        ];

        foreach ($enseignants as $enseignantData) {
            Enseignant::create($enseignantData);
        }

        // Créer des matières de test
        $matieres = [
            [
                'nom' => 'Mathématiques',
                'niveau' => '6ème',
                'coefficient' => 4.0,
                'description' => 'Mathématiques niveau 6ème'
            ],
            [
                'nom' => 'Français',
                'niveau' => '6ème',
                'coefficient' => 4.0,
                'description' => 'Français niveau 6ème'
            ],
            [
                'nom' => 'Histoire-Géographie',
                'niveau' => '6ème',
                'coefficient' => 3.0,
                'description' => 'Histoire-Géographie niveau 6ème'
            ],
            [
                'nom' => 'Sciences',
                'niveau' => '6ème',
                'coefficient' => 3.0,
                'description' => 'Sciences niveau 6ème'
            ],
            [
                'nom' => 'Mathématiques',
                'niveau' => '5ème',
                'coefficient' => 4.0,
                'description' => 'Mathématiques niveau 5ème'
            ],
            [
                'nom' => 'Français',
                'niveau' => '5ème',
                'coefficient' => 4.0,
                'description' => 'Français niveau 5ème'
            ]
        ];

        foreach ($matieres as $matiereData) {
            Matiere::create($matiereData);
        }

        $this->command->info('Données de test créées avec succès !');
        $this->command->info('Utilisateurs de test :');
        $this->command->info('- Admin: admin@ecole.com / password123');
        $this->command->info('- Enseignant: jean.dupont@ecole.com / password123');
        $this->command->info('- Élève: pierre.durand@ecole.com / password123');
        $this->command->info('- Parent: sophie.parent@ecole.com / password123');
    }
}
