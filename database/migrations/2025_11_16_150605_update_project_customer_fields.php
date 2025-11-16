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
        // Check if column exists before modifying
        if (Schema::hasColumn('project_customer_fields', 'user_id')) {
            // Drop foreign key if it exists
            try {
                Schema::table('project_customer_fields', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist or have a different name, continue
            }

            // Drop and recreate the column to change its position
            Schema::table('project_customer_fields', function (Blueprint $table) {
                $table->dropColumn(['user_id']);
            });
        }

        // Add the column with the new position only if it doesn't exist
        if (!Schema::hasColumn('project_customer_fields', 'user_id')) {
            Schema::table('project_customer_fields', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('project_customer_id');
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if column exists before modifying
        if (Schema::hasColumn('project_customer_fields', 'user_id')) {
            // Drop foreign key if it exists
            try {
                Schema::table('project_customer_fields', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist or have a different name, continue
            }

            // Drop the column
            Schema::table('project_customer_fields', function (Blueprint $table) {
                $table->dropColumn(['user_id']);
            });
        }

        // Restore the column to its original position (after id, before admin_id)
        if (!Schema::hasColumn('project_customer_fields', 'user_id')) {
            Schema::table('project_customer_fields', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->nullOnDelete();
            });
        }
    }
};
