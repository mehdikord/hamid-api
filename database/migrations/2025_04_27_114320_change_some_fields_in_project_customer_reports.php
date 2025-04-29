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
        Schema::table('project_customer_reports', function (Blueprint $table) {
            $table->dropColumn('files');
            $table->string('file_url')->nullable()->after('report');
            $table->string('file_path')->nullable()->after('file_url');
            $table->string('file_size')->nullable()->after('file_path');
            $table->string('file_name')->nullable()->after('file_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_customer_reports', function (Blueprint $table) {
            //
        });
    }
};
