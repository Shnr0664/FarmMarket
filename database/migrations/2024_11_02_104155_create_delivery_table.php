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
        Schema::create('Delivery', function (Blueprint $table) {
            $table->id('DeliveryID');
            $table->unsignedBigInteger('OrderID');
            $table->string('DeliveryLoc', 255);
            $table->date('DShipDate');
            $table->string('DeliveryStatus', 50);
            $table->date('DFinishDate');

            $table->foreign('OrderID')->references('OrderID')->on('Order')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery');
    }
};
