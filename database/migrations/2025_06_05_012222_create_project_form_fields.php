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
        Schema::create('project_form_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_form_id');
            $table->unsignedBigInteger('field_id');
            $table->string('title')->nullable();
            $table->timestamps();
            $table->foreign('project_form_id')->references('id')->on('project_forms')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('field_id')->references('id')->on('fields')->cascadeOnDelete()->cascadeOnUpdate();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_form_fields');
    }
};
