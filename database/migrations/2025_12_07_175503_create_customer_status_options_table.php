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
        Schema::create('customer_status_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_status_id');
            $table->unsignedBigInteger('message_option_id');
            $table->timestamps();
            $table->foreign('customer_status_id')->references('id')->on('user_project_customer_statuses')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('message_option_id')->references('id')->on('status_message_options')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_status_options');
    }
};
