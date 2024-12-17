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
            $table->string('type')->index();
            $table->foreignId('api_key_id')->nullable()->constrained('api_keys')->nullOnDelete();
            $table->string('order_status')->index();
            $table->string('fulfillment_status')->index();
            $table->string('refund_status')->index();
            $table->string('mapped_status');

            $table->string('order_id')->unique();
            $table->dateTime('order_date')->nullable();
            $table->timestamp('update_date')->nullable();
            $table->string('fulfillment')->nullable();
            $table->string('sales_channel')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->string('total_currency');
            $table->string('payment_method')->nullable();

            // Адрес и данные покупателя
            $table->string('buyer_name')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country_code')->nullable();

            // Показатели обработки и отгрузки
            $table->dateTime('expected_ship_date')->nullable();

            $table->boolean('is_shipped')->default(false);

            // Оригинальный объект
            $table->json('original_object')->nullable();
            // $table->text('original_object')->nullable();
            // $table->longText('original_object')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
