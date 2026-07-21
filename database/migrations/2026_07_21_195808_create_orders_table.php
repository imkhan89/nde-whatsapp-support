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
        Schema::create('orders', function (Blueprint $table) {
    $table->id();

    $table->foreignId('customer_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->string('shopify_order_id')->unique();

    $table->string('order_number');

    $table->string('status')->default('pending');

    $table->decimal('total', 12, 2)->default(0);

    $table->timestamps();
});
    
    }
};
