<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents_vehicule', function (Blueprint $table) {
            $table->id('idDocument');
            $table->foreignId('voiture_id')->references('idVoiture')->on('voitures')->onDelete('cascade');
            $table->enum('type', ['Carte grise', 'Assurance', 'Contrôle technique', 'Garantie', 'Facture achat', 'Autre']);
            $table->string('nom_fichier');
            $table->string('chemin');
            $table->date('date_expiration')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents_vehicule');
    }
};
