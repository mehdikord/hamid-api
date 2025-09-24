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
        Schema::table('project_customers', function (Blueprint $table) {
            $table->string('target_price')->nullable()->after('project_level_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_customers', function (Blueprint $table) {
            $table->dropColumn('target_price');
        });
    }
};
