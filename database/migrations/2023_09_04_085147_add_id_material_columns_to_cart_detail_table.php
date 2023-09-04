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
        Schema::table('cart_detail', function (Blueprint $table) {
            $table->integer('ID_Material')->nullable()->change();
            $table->foreign('ID_Material')->references('ID_Material')->on('material')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_detail', function (Blueprint $table) {
            //
        });
    }
};
