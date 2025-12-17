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
        Schema::table('project_customer_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('project_product_id')->nullable()->after('project_id');
            $table->foreign('project_product_id')->references('id')->on('project_products')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_customer_invoices', function (Blueprint $table) {
            //
        });
    }
};
