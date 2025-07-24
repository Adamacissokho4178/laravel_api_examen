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
        Schema::create('eleves', function (Blueprint $table) {
            $table->id();
            $table->string('prenom');
            $table->string('nom');
            $table->string('email')->unique();
            $table->date('date_naissance');
            $table->unsignedBigInteger('classe_id')->nullable();
            $table->string('chemin_document')->nullable(); // justificatif PDF ou autre
            $table->unsignedBigInteger('utilisateur_id')->nullable(); // identifiant user lié
            $table->timestamps();

            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('set null');
            $table->foreign('utilisateur_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eleves');
    }
};
