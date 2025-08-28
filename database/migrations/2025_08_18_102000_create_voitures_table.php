<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('voitures', function (Blueprint $table) {
            $table->id('idVoiture');
            $table->string('marque');
            $table->string('modele');
            $table->year('annee');
            $table->double('kilometrage');
            $table->string('etat');
            $table->enum('statut', ['En boutique', 'En location', 'En maintenance']);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('voitures');
    }
};