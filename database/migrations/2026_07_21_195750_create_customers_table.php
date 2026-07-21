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
        Schema::create('customers', function (Blueprint $table) {
    $table->id();

    $table->string('shopify_customer_id')->nullable()->unique();

    $table->string('first_name')->nullable();

    $table->string('last_name')->nullable();

    $table->string('phone')->nullable()->unique();

    $table->string('email')->nullable();

    $table->timestamps();
});
    
    }
};
