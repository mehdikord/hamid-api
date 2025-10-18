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
        Schema::create('project_customer_status_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_status_id');
            $table->unsignedBigInteger('status_message_id');
            $table->foreign('customer_status_id')->references('id')->on('project_customer_statuses')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('status_message_id')->references('id')->on('status_messages')->cascadeOnDelete()->cascadeOnUpdate();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_customer_status_messages');
    }
};
