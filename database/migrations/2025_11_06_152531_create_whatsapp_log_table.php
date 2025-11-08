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
        Schema::create('whatsapp_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('whatsapp_number_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('phone')->nullable();
            $table->text('message')->nullable();
            $table->string('is_success')->default(true);
            $table->timestamps();
            $table->foreign('admin_id')->references('id')->on('admins')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('whatsapp_number_id')->references('id')->on('whatsapp_numbers')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_log');
    }
};
