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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->foreignId('customer_id')->constrained();
            $table->string('courier');
            $table->string('service');
            $table->bigInteger('cost_courier');
            $table->integer('weight');
            $table->string('name');
            $table->bigInteger('phone');
            $table->text('address');
            $table->enum('Status', ['Pending', 'Success', 'Failed', 'Expired']);
            $table->bigInteger('grand_total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
