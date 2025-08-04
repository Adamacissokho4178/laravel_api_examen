<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si les colonnes existent déjà
        if (!Schema::hasColumn('users', 'nom')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nom')->after('id');
            });
        }
        
        if (!Schema::hasColumn('users', 'prenom')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('prenom')->after('nom');
            });
        }
        
        // Mettre à jour les données existantes si la colonne name existe
        if (Schema::hasColumn('users', 'name')) {
            DB::table('users')->update([
                'nom' => DB::raw('SUBSTRING_INDEX(name, " ", 1)'),
                'prenom' => DB::raw('SUBSTRING_INDEX(name, " ", -1)')
            ]);
            
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
        
        // Modifier l'enum role si nécessaire
        if (Schema::hasColumn('users', 'role')) {
            // Mettre à jour les données problématiques
            DB::table('users')->where('role', 'eleve_parent')->update(['role' => 'eleve']);
            
            // Modifier l'enum
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'enseignant', 'eleve', 'parent') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration ne peut pas être facilement annulée
        // car elle modifie la structure de la table
    }
};
