<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
            $table->id('idNotification');
            $table->foreignId('user_id')->constrained('users', 'idUser')->onDelete('cascade');
            $table->enum('type', [
                'document_expiring',
                'document_expired',
                'alert_generated',
                'alert_critical',
                'intervention_created',
                'intervention_completed',
                'intervention_upcoming',
                'system_announcement',
                'vehicle_maintenance_due'
            ]);
            $table->string('title');
            $table->text('message');
            $table->json('metadata')->nullable(); // Store related IDs, links, etc.
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
