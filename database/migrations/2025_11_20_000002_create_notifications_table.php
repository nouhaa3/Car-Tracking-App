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
        // Check if table already exists
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id('idNotification');
                $table->unsignedBigInteger('user_id'); // Admin who receives the notification
                $table->string('type'); // 'pending_registration', 'message', etc.
                $table->string('title');
                $table->text('message');
                $table->json('metadata')->nullable(); // Store pending_user_id, etc.
                $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'is_read']);
            });
        } else {
            // If table exists, just ensure we have the right columns
            Schema::table('notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('notifications', 'metadata')) {
                    $table->json('metadata')->nullable()->after('message');
                }
                if (!Schema::hasColumn('notifications', 'priority')) {
                    $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium')->after('metadata');
                }
                if (!Schema::hasColumn('notifications', 'is_read')) {
                    $table->boolean('is_read')->default(false)->after('priority');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
