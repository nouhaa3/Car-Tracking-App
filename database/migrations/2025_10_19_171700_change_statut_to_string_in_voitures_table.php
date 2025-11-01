<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('voitures', function (Blueprint $table) {
            $table->string('statut')->change();
        });
    }

    public function down(): void {
        Schema::table('voitures', function (Blueprint $table) {
            $table->enum('statut', ['En boutique', 'En location', 'En maintenance'])->change();
        });
    }
};
