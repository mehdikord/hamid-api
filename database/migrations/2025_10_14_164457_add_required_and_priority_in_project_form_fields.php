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
        Schema::table('project_form_fields', function (Blueprint $table) {
            $table->boolean('required')->default(false)->after('title');
            $table->integer('priority')->default(1)->after('required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_form_fields', function (Blueprint $table) {
            //
        });
    }
};
