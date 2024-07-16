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
        Schema::create('bot_work_flows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('message')->nullable();
            $table->string('type');
            $table->string('conditional')->nullable();
            $table->unsignedBigInteger('parent')->nullable();            
            $table->unsignedBigInteger('bot_id');          
            $table->unsignedBigInteger('status_id');            
            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('status');
            $table->foreign('bot_id')->references('id')->on('bots');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_work_flows');
    }
};
