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
        Schema::create('Payment', function (Blueprint $table) {
            $table->id('PaymentID');
            $table->unsignedBigInteger('OrderID');
            $table->string('PaymentMethod', 50);
            $table->date('PaymentDate');
            $table->decimal('TotalAmount', 10, 2);

            $table->foreign('OrderID')->references('OrderID')->on('Order')
                ->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
