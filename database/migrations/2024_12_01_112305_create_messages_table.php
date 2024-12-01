<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up() : void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');  // ID of the sender
            $table->unsignedBigInteger('receiver_id');  // ID of the receiver
            $table->text('message');  // The actual message
            $table->string('attachment')->nullable();  // Optional file attachment (base64 string or file path)
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('set null');
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('messages');
    }
}

