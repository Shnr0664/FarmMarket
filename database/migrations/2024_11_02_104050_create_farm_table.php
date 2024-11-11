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
        Schema::create('Farm', function (Blueprint $table) {
            $table->id('FarmID');
            $table->unsignedBigInteger('FarmerID');
            $table->string('FarmName');
            $table->decimal('FarmSize', 10, 2);
            $table->string('CropsTypes');

            $table->foreign('FarmerID')->references('FarmerID')->on('Farmer')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm');
    }
};
