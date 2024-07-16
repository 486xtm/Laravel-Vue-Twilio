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
        Schema::create('lead_archive_reasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reason');
            $table->unsignedBigInteger('lead_id');
            $table->timestamps();
        
            $table->foreign('lead_id')->references('id')->on('leads')
            ->onDelete('cascade');

            $table->foreign('reason')->references('id')->on('archive_reasons')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_archive_reasons');
    }
};
