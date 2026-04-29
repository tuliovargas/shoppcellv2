<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('quantity');
            $table->decimal('value', 8, 2);
            $table->decimal('total_usado', 8, 2)->default(0); // valor total usado do cupom
            $table->boolean('valido')->default(true); // informa se o cupon ainda pode ser usado (ainda tem saldo)

            $table->foreignId('user_id')->constrained(); // Usuário que criou o cupom
            $table->foreignId('order_id')->nullable()->constrained(); // Pedido que originou o cupom

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('coupons');
    }
}
