<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('interventions', function (Blueprint $table) {
            $table->id('idIntervention');
            $table->string('type');
            $table->date('date');
            $table->double('cout');
            $table->string('garage');
            $table->text('remarques')->nullable();
            $table->foreignId('voiture_id')->references('idVoiture')->on('voitures')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('interventions');
    }
};