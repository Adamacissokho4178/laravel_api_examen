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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('eleve_id');
            $table->unsignedBigInteger('matiere_id');
            $table->unsignedBigInteger('enseignant_id');
            $table->string('periode'); // trimestre, semestre, etc.
            $table->decimal('note', 4, 2); // note sur 20 avec 2 décimales
            $table->text('appreciation')->nullable(); // commentaire de l'enseignant
            $table->decimal('coefficient', 3, 1)->default(1.0); // coefficient de la matière
            $table->string('type_evaluation')->default('controle'); // controle, examen, etc.
            $table->date('date_evaluation');
            $table->timestamps();

            // Clés étrangères
            $table->foreign('eleve_id')->references('id')->on('eleves')->onDelete('cascade');
            $table->foreign('matiere_id')->references('id')->on('matieres')->onDelete('cascade');
            $table->foreign('enseignant_id')->references('id')->on('enseignants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
