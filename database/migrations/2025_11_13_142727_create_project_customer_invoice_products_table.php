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
        Schema::create('project_customer_invoice_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_customer_invoices_id');
            $table->unsignedBigInteger('project_product_id')->nullable();
            $table->timestamps();

            $table->foreign('project_customer_invoices_id')->references('id')->on('project_customer_invoices')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('project_product_id')->references('id')->on('project_products')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_customer_invoice_products');
    }
};
