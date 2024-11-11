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
        Schema::create('Product', function (Blueprint $table) {
            $table->id('ProductID');
            $table->unsignedBigInteger('FarmID');
            $table->string('ProductName');
            $table->integer('ProductQuantity');
            $table->string('ProductCategory');
            $table->string('ProductDesc');
            $table->decimal('ProductPrice', 10, 2);
            $table->string('ProductImg');

            $table->foreign('FarmID')->references('FarmID')->on('Farm')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
