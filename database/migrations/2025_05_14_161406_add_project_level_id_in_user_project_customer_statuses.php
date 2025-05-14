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
        Schema::table('user_project_customer_statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('project_level_id')->nullable()->after('customer_status_id');
            $table->foreign('project_level_id')->references('id')->on('project_levels')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_project_customer_statuses', function (Blueprint $table) {
            //
        });
    }
};
