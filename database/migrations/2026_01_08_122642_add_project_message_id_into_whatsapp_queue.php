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
        Schema::table('whatsapp_queue', function (Blueprint $table) {
            $table->unsignedBigInteger('project_message_id')->nullable()->after('project_id');
            $table->string('link')->nullable()->after('project_message_id');
            $table->foreign('project_message_id')->references('id')->on('project_messages')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_queue', function (Blueprint $table) {
            //
        });
    }
};
