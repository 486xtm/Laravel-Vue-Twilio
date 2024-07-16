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
        Schema::create('form_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->bigInteger('facebook_form_id');
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('status_id');
            $table->timestamps();

            $table->foreign('campaign_id')->references('id')->on('campaigns')
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
        Schema::dropIfExists('form_campaigns');
    }
};
