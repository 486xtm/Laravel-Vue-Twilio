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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('celular');
            $table->unsignedBigInteger('canal_id')->nullable();
            $table->unsignedBigInteger('origem_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('mensagem')->nullable();
            $table->timestamps();

            // $table->foreign('canal_id')->references('id')->on('channels');
            $table->foreign('canal_id')->references('id')->on('canals');
            $table->foreign('origem_id')->references('id')->on('origems');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
