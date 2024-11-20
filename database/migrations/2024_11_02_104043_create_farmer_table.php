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
        Schema::create('Farmer', function (Blueprint $table) {
            $table->id('FarmerID');
            $table->unsignedBigInteger('UserID');

            $table->foreign('UserID')->references('UserID')->on('User')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('Farmer', function (Blueprint $table) {
            $table->boolean('IsApproved')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Farmer', function (Blueprint $table) {
            $table->dropColumn('IsApproved');
        });
        
        Schema::dropIfExists('farmer');
    }
};
