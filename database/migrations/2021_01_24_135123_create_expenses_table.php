<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_info_id')->nullable()->constrained();
            $table->string('name', 50);
            $table->string('invoice')->nullable();
            $table->date('payday');
            $table->decimal('value', 8, 2);
            $table->enum('installments', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'])->default('1');
            $table->text('observation')->nullable();

            $table->foreignId('supplier_id')->constrained();
            $table->foreignId('payment_method_id')->constrained();
            $table->foreignId('expense_type_id')->constrained();

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
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropForeign(['expense_type_id']);
        });
        Schema::dropIfExists('expenses');
    }
}
