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
        // Check if table exists first
        if (!Schema::hasTable('contact_messages')) {
            Schema::create('contact_messages', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->string('prenom');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->text('message');
                $table->boolean('is_read')->default(0);
                $table->text('admin_reply')->nullable();
                $table->timestamp('replied_at')->nullable();
                $table->unsignedBigInteger('replied_by')->nullable();
                $table->timestamps();

                // Foreign key to users table
                $table->foreign('replied_by')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null');
            });
        } else {
            // Table exists, just add the new columns if they don't exist
            Schema::table('contact_messages', function (Blueprint $table) {
                if (!Schema::hasColumn('contact_messages', 'admin_reply')) {
                    $table->text('admin_reply')->nullable()->after('message');
                }
                if (!Schema::hasColumn('contact_messages', 'replied_at')) {
                    $table->timestamp('replied_at')->nullable()->after('admin_reply');
                }
                if (!Schema::hasColumn('contact_messages', 'replied_by')) {
                    $table->unsignedBigInteger('replied_by')->nullable()->after('replied_at');
                    $table->foreign('replied_by')
                          ->references('id')
                          ->on('users')
                          ->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('contact_messages')) {
            Schema::table('contact_messages', function (Blueprint $table) {
                if (Schema::hasColumn('contact_messages', 'replied_by')) {
                    $table->dropForeign(['replied_by']);
                }
            });
            
            Schema::dropIfExists('contact_messages');
        }
    }
};
