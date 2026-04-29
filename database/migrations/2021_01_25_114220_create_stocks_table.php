<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['in', 'out']);
            $table->string('purchase_invoice')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained();
            $table->foreignId('payment_method_id')->nullable()->constrained();
            $table->foreignId('order_id')->nullable()->constrained();
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
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['payment_method_id']);
            $table->dropForeign(['order_id']);
        });
        Schema::dropIfExists('stocks');
    }
}
