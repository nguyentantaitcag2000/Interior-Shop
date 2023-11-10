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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->unsignedBigInteger('reply_to')->nullable();
            $table->integer('ID_Product');
            $table->integer('ID_User');
            $table->timestamps();

            $table->foreign('ID_Product')->references('ID_Product')->on('product')->cascadeOnDelete();
            $table->foreign('ID_User')->references('ID_User')->on('users')->cascadeOnDelete();
            $table->foreign('reply_to')->references('id')->on('comments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
