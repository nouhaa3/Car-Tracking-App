<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documents', function (Blueprint $table) {
            $table->id('idDocument');
            $table->string('nomFichier');
            $table->string('typeFichier');
            $table->string('chemin');
            $table->date('dateUpload');
            $table->foreignId('intervention_id')->references('idIntervention')->on('interventions')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('documents');
    }
};
