<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('item_id')->index();
            $table->string('api_order_id')->index();
            $table->integer('quantity')->default(0);
            $table->string('title')->nullable();
            $table->string('sku');

            // Связь с заказом
            //            $table->foreign('api_order_id')
            //                ->references('order_id')
            //                ->on('orders')
            //                ->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
