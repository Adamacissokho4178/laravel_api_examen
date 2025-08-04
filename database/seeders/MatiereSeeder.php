<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Matiere;

class MatiereSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matieres = [
            [
                'nom' => 'Mathématiques',
                'niveau' => '6ème',
                'coefficient' => 4.0,
                'description' => 'Mathématiques pour la 6ème année - Algèbre, géométrie et arithmétique'
            ],
            [
                'nom' => 'Français',
                'niveau' => '6ème',
                'coefficient' => 4.0,
                'description' => 'Français pour la 6ème année - Grammaire, conjugaison et littérature'
            ],
            [
                'nom' => 'Histoire-Géographie',
                'niveau' => '6ème',
                'coefficient' => 3.0,
                'description' => 'Histoire et Géographie pour la 6ème année'
            ],
            [
                'nom' => 'Sciences',
                'niveau' => '6ème',
                'coefficient' => 3.0,
                'description' => 'Sciences pour la 6ème année - SVT et physique-chimie'
            ],
            [
                'nom' => 'Anglais',
                'niveau' => '6ème',
                'coefficient' => 2.0,
                'description' => 'Anglais pour la 6ème année'
            ],
            [
                'nom' => 'Mathématiques',
                'niveau' => '5ème',
                'coefficient' => 4.0,
                'description' => 'Mathématiques pour la 5ème année'
            ],
            [
                'nom' => 'Français',
                'niveau' => '5ème',
                'coefficient' => 4.0,
                'description' => 'Français pour la 5ème année'
            ]
        ];

        foreach ($matieres as $matiere) {
            Matiere::create($matiere);
        }
    }
}
