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
        Schema::table('user_reminders', function (Blueprint $table) {
            $table->unsignedBigInteger('project_customer_id')->nullable()->after('user_id');
            $table->foreign('project_customer_id')->references('id')->on('project_customers')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_reminders', function (Blueprint $table) {
            //
        });
    }
};
