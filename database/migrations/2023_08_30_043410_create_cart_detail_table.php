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
        Schema::create('cart_detail', function (Blueprint $table) {
            $table->integer('ID_SC');
            $table->integer('ID_Product');
            $table->integer('ID_Color');
            $table->integer('Amount');
            $table->timestamps();

             // Foreign keys
            $table->foreign('ID_SC')->references('ID_SC')->on('shoppingcart')->onDelete('cascade');
            $table->foreign('ID_Product')->references('ID_Product')->on('product')->onDelete('cascade');
            $table->foreign('ID_Color')->references('ID_Color')->on('color')->onDelete('cascade');
            // Khóa chính tổ hợp
            $table->primary(['ID_SC', 'ID_Product']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_detail');
    }
};
