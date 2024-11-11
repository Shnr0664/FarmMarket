<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up(): void
    {
        Schema::create('Order', function (Blueprint $table) {
            $table->id('OrderID');
            $table->unsignedBigInteger('BuyerID');
            $table->date('OrderDate');
            $table->decimal('TotalAmount', 10, 2);
            $table->string('OrderStatus', 50);

            $table->foreign('BuyerID')->references('BuyerID')->on('Buyer')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
