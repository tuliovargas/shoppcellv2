<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('value', 8, 2);
            $table->enum('brand_card', ['mastercard', 'visa', 'american_express', 'elo'])->nullable();
            $table->string('pix_number')->nullable();
            $table->string('check_number')->nullable();
            $table->string('check_name')->nullable();

            $table->foreignId('order_id')->constrained();
            $table->foreignId('payment_method_id')->constrained();
            $table->foreignId('tax_installment_id')->nullable()->constrained();
            $table->foreignId('bank_id')->nullable()->constrained();
            $table->foreignId('cashier_info_id')->nullable()->constrained(); // ID do caixa que o pedido foi efetuado

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
        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['payment_method_id']);
            $table->dropForeign(['tax_installment_id']);
            $table->dropForeign(['bank_id']);
            $table->dropForeign(['cashier_info_id']);
        });
        Schema::dropIfExists('order_payments');
    }
}
