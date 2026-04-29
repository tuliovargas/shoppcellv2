<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashiers', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['in', 'out']);
            $table->decimal('total_value', 8, 2);
            $table->text('note')->nullable();

            $table->foreignId('order_id')->nullable()->constrained();
            $table->foreignId('expense_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();

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
        Schema::table('cashiers', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['expense_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('cashiers');
    }
}
