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
            $table->dropForeign('project_customers_project_customer_status_id_foreign');
            $table->foreign('project_customer_status_id')->references('id')->on('project_customer_statuses')->cascadeOnUpdate()->nullOnDelete();
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
