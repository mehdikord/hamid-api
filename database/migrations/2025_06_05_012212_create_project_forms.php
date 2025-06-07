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
        Schema::create('project_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('view')->default('0')->nullable();
            $table->string('register')->default('0')->nullable();
            $table->string('token')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_forms');
    }
};
