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
        Schema::create('lead_messages', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('status_id');
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('leads')
            ->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('status')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_messages');
    }
};
