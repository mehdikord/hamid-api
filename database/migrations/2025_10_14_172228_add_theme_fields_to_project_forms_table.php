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
        Schema::table('project_forms', function (Blueprint $table) {
            $table->string('theme_name')->nullable()->after('description');
            $table->string('theme_color')->nullable()->after('theme_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_forms', function (Blueprint $table) {
            $table->dropColumn(['theme_name', 'theme_color']);
        });
    }
};
