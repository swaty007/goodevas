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
        Schema::table('warehouses', function (Blueprint $table) {
            // Добавляем новый столбец ysell_name
            $table->string('ysell_name')->nullable()->unique();
        });

        // Копируем данные из поля name в поле ysell_name
        DB::table('warehouses')->update([
            'ysell_name' => DB::raw('name'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn('ysell_name');
        });
    }
};
