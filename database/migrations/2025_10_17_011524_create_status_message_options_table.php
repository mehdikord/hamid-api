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
        Schema::create('status_message_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('status_message_id');
            $table->string('option')->nullable();
            $table->foreign('status_message_id')->references('id')->on('status_messages')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_message_options');
    }
};
