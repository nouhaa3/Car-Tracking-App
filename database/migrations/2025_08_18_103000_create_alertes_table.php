<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alertes', function (Blueprint $table) {
            $table->id('idAlerte');
            $table->string('type');
            $table->date('dateEchance');
            $table->double('kilometrageEchance');
            $table->enum('statut', ['En attente', 'Traité'])->default('En attente');
            $table->foreignId('voiture_id')->references('idVoiture')->on('voitures')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('alertes');
    }
};
