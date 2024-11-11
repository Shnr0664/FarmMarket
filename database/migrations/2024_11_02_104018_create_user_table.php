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
        Schema::create('User', function (Blueprint $table) {
            $table->id('UserID');
            $table->string('Password');
            $table->string('ProfilePic')->nullable();
        });

        Schema::create('Admin', function (Blueprint $table) {
            $table->id('AdminID');
            $table->unsignedBigInteger('UserID')->nullable();

            $table->foreign('UserID')->references('UserID')->on('User')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('PersonalInfo', function (Blueprint $table) {
            $table->id('InfoID');
            $table->unsignedBigInteger('UserID');
            $table->string('Name');
            $table->string('Email')->unique();
            $table->string('PhoneNumber');
            $table->string('UserAddress');

            $table->foreign('UserID')->references('UserID')->on('User')
                ->onDelete('cascade')->onUpdate('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PersonalInfo');
        Schema::dropIfExists('admin');
        Schema::dropIfExists('user');
    }
};
