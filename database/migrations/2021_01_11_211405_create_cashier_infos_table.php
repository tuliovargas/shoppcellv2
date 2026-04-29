<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashierInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashier_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Usuário que abriu o caixa
            $table->decimal('charge', 10, 2); // Troco do caixa, inicial começa com o valor do caixa anterior
            $table->decimal('deposit', 10, 2)->default(0.0); // Valor a ser depositado no caixa
            $table->timestamp('close_time')->nullable(); // Hora do fechamento, null se não foi fechado
            $table->json('difference')->nullable(); // Hora do fechamento, null se não foi fechado
            $table->text('observation_open')->nullable();
            $table->text('observation_close')->nullable();
            $table->timestamps(); // Hora da abertura e o hora da ultima modificação
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cashier_infos');
    }
}
