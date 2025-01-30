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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('project_category_id')->nullable();
            $table->unsignedBigInteger('project_status_id')->nullable();
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->string('manager_name')->nullable();
            $table->string('manager_phone')->nullable();
            $table->text('description')->nullable();
            $table->string('start_at')->nullable();
            $table->string('end_at')->nullable();
            $table->string('customers')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('project_category_id')->references('id')->on('project_categories')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('project_status_id')->references('id')->on('project_statuses')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('created_by')->references('id')->on('admins')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('updated_by')->references('id')->on('admins')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
