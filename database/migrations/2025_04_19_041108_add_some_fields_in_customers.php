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
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('province_id')->nullable()->after('id');
            $table->unsignedBigInteger('city_id')->nullable()->after('province_id');
            $table->string('national_code')->nullable()->after('email');
            $table->string('gender')->nullable()->default('male')->after('national_code');
            $table->string('tel')->nullable()->after('image');
            $table->string('address')->nullable()->after('tel');
            $table->string('postal_code')->nullable()->after('address');
            $table->string('birthday')->nullable()->after('postal_code');
            $table->foreign('province_id')->references('id')->on('provinces')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('city_id')->references('id')->on('cities')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
};
