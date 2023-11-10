<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_price_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedDouble('price');
            $table->integer('ID_Product');
            $table->timestamp('date_effect')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            //

            $table->foreign('ID_Product')->references('ID_Product')->on('product')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_histories');
    }
};
