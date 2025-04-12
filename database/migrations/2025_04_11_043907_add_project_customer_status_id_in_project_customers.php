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
        Schema::table('project_customers', function (Blueprint $table) {
            $table->unsignedBigInteger('project_customer_status_id')->nullable()->after('customer_id');
            $table->foreign('project_customer_status_id')->references('id')->on('project_customer_statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_customers', function (Blueprint $table) {
            //
        });
    }
};
