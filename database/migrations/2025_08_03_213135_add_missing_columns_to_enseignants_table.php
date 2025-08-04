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
        Schema::table('enseignants', function (Blueprint $table) {
            $table->string('nom', 50)->after('id');
            $table->string('prenom', 50)->after('nom');
            $table->string('email')->unique()->after('prenom');
            $table->string('telephone', 20)->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enseignants', function (Blueprint $table) {
            $table->dropColumn(['nom', 'prenom', 'email', 'telephone']);
        });
    }
};
