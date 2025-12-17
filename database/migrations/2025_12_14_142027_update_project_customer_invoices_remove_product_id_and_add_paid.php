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
        // Drop foreign key and remove project_product_id from project_customer_invoices
        Schema::table('project_customer_invoices', function (Blueprint $table) {
            $table->dropForeign(['project_product_id']);
            $table->dropColumn('project_product_id');
        });

        // Drop project_customer_prices table
        Schema::dropIfExists('project_customer_prices');

        // Add paid field after target_price
        Schema::table('project_customer_invoices', function (Blueprint $table) {
            $table->boolean('paid')->default(0)->after('target_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove paid field
        Schema::table('project_customer_invoices', function (Blueprint $table) {
            $table->dropColumn('paid');
        });

        // Recreate project_customer_prices table
        Schema::create('project_customer_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('project_product_id');
            $table->unsignedBigInteger('project_customer_id');
            $table->string('price')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('project_customer_id')->references('id')->on('project_customers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('project_product_id')->references('id')->on('project_products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });

        // Add back project_product_id to project_customer_invoices
        Schema::table('project_customer_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('project_product_id')->nullable()->after('project_id');
            $table->foreign('project_product_id')->references('id')->on('project_products')->cascadeOnUpdate()->nullOnDelete();
        });
    }
};
