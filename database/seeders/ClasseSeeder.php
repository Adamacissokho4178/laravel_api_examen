<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Classe;

class ClasseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            ['nom' => '6ème A', 'niveau' => '6ème', 'capacite' => 30],
            ['nom' => '6ème B', 'niveau' => '6ème', 'capacite' => 30],
            ['nom' => '5ème A', 'niveau' => '5ème', 'capacite' => 30],
            ['nom' => '5ème B', 'niveau' => '5ème', 'capacite' => 30],
            ['nom' => '4ème A', 'niveau' => '4ème', 'capacite' => 30],
            ['nom' => '4ème B', 'niveau' => '4ème', 'capacite' => 30],
            ['nom' => '3ème A', 'niveau' => '3ème', 'capacite' => 30],
            ['nom' => '3ème B', 'niveau' => '3ème', 'capacite' => 30],
        ];

        foreach ($classes as $classe) {
            Classe::create($classe);
        }
    }
}
