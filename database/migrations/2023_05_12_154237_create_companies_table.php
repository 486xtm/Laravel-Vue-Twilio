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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document');
            $table->string('address');
            $table->string('number');
            $table->string('complement');
            $table->string('region');
            $table->string('zip');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('state_id');
            $table->string('accountable');
            $table->string('email');
            $table->string('phone');
            $table->timestamps();
            
            $table->foreign('city_id')->references('id')->on('cidades');
            $table->foreign('state_id')->references('id')->on('estados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
