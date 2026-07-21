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
        Schema::create('messages', function (Blueprint $table) {
    $table->id();

    $table->foreignId('customer_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->string('wa_message_id')->nullable();

    $table->enum('direction', [
        'incoming',
        'outgoing',
    ]);

    $table->text('message');

    $table->timestamps();
    });
    
    }
};
