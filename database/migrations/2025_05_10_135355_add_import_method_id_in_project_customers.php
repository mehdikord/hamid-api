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
            $table->unsignedBigInteger('import_method_id')->nullable()->after('customer_id');
            $table->foreign('import_method_id')->references('id')->on('import_methods')->cascadeOnUpdate()->nullOnDelete();
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
