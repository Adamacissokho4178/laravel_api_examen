<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la colonne existe et la modifier
        if (Schema::hasColumn('enseignants', 'utilisateur_id')) {
            Schema::table('enseignants', function (Blueprint $table) {
                $table->unsignedBigInteger('utilisateur_id')->nullable()->change();
            });
        } else {
            Schema::table('enseignants', function (Blueprint $table) {
                $table->unsignedBigInteger('utilisateur_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enseignants', function (Blueprint $table) {
            $table->unsignedBigInteger('utilisateur_id')->nullable(false)->change();
        });
    }
};
