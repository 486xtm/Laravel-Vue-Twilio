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
        Schema::table('payment_reconciliations', function (Blueprint $table) {
            $table->string('parcel')->after('deal')->nullable();   
            $table->decimal('total_deal', 16, 2)->default(0)->after('parcel');
            $table->decimal('comission', 16, 6)->default(0)->after('total_deal');
            $table->timestamp('sell_date')->nullable()->after('comission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_reconciliations', function (Blueprint $table) {
            Schema::dropIfExists('parcel','total_deal','comission','sell_date');
        });
    }
};
