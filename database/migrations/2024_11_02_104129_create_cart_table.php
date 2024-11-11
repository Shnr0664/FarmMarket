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
        Schema::create('Cart', function (Blueprint $table) {
            $table->unsignedBigInteger('BuyerID');
            $table->unsignedBigInteger('ProductID');
            $table->decimal('TotalAmount', 10, 2);
            $table->string('CartItems', 255);

            // Composite primary key
            $table->primary(['BuyerID', 'ProductID']);

            // Foreign key references
            $table->foreign('BuyerID')->references('BuyerID')->on('Buyer')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ProductID')->references('ProductID')->on('Product')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};
