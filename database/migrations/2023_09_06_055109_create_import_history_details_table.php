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
        Schema::create('import_history_details', function (Blueprint $table) {
            $table->id();
            $table->float('Price_IDH');
            $table->integer('Amount_IDH');
            $table->integer('ID_Color')->nullable();
            $table->integer('ID_Material')->nullable();
            $table->integer('ID_D')->nullable();
            $table->integer('ID_Product');
            $table->integer('ID_IH');
            $table->timestamps();

            //

            $table->foreign('ID_Color')->references('ID_Color')->on('color')->cascadeOnDelete();
            $table->foreign('ID_Material')->references('ID_Material')->on('material')->cascadeOnDelete();
            $table->foreign('ID_Product')->references('ID_Product')->on('product')->cascadeOnDelete();
            $table->foreign('ID_D')->references('ID_D')->on('dimensions')->cascadeOnDelete();
            $table->foreign('ID_IH')->references('ID_IH')->on('import_history')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_history_details');
    }
};
