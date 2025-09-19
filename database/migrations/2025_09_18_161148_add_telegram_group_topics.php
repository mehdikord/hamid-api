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
        Schema::create('telegram_group_topics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('telegram_group_id');
            $table->string('topic_id');
            $table->string('name');
            $table->foreign('telegram_group_id')->references('id')->on('telegram_groups')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_group_topics');
    }
};
