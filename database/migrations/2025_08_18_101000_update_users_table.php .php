<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            /*$table->unsignedBigInteger('role_id')->nullable()->after('id');*/
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('idRole')->on('roles')->onDelete('cascade');

            $table->string('prenom')->nullable();
            $table->foreign('role_id')->references('idRole')->on('roles')->onDelete('set null');

        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('prenom');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};