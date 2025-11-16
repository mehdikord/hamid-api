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
            $table->unsignedBigInteger('import_method_id')->nullable()->after('name');
            $table->unsignedBigInteger('tag_id')->nullable()->after('import_method_id');
            $table->foreign('import_method_id')->references('id')->on('import_methods')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_forms', function (Blueprint $table) {
            //
        });
    }
};
