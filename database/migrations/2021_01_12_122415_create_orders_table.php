<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('subtotal', 8, 2)->default(0.00);
            $table->decimal('discount', 8, 2)->default(0.00);
            $table->decimal('total', 8, 2)->default(0.00);
            $table->text('note')->nullable();
            $table->enum('status', [
                'is_budget' , //  Orçamentos 
                'is_request', // O produto é um pedido para a compra pelo estabelecimento
                'waiting_approval', //O produto é um item de manutenção como consertar computador e está aguardando o cliente aprovar o conserto
                'approved', //O produto é um item de manutenção como consertar computador e está aprovado o inicio do conserto
                'waiting_product', //O produto é um item de manutenção como consertar computador, mas ainda não tem a peça necessária no estoque
                'maintenance', //O produto é um item de manutenção como consertar computador e está no processo de manutenção
                'waiting_payment', //Produto ou serviço concluído que está aguardando pagamento do cliente
                'concluded', //Compra de produto ou serviço concluído
                'canceled', //Compra de produto ou serviço cancelado
                'returned', //Compra de produto devolvido ou valor de serviço devolvido
                'waiting_maintenance', // aguardando manutenção
                'partially_returned' // parcialmente devolvida
            ])->nullable();
            $table->boolean('is_warranty')->nullable(); // Verdadeiro ou falso caso o pedido seja garantia
            $table->date('delivery_date')->nullable(); // Data de entrega se for manutenção
            $table->timestamp('canceled_at')->nullable(); // data de cancelamento do pedido
            $table->text('cancellation_observation')->nullable(); // observação do cancelamento
            $table->bigInteger('seller_id')->unsigned()->nullable(); // Vendedor que fez a venda
            $table->bigInteger('canceled_user_id')->unsigned()->nullable(); // Usuário que cancelou o pedido

            $table->foreignId('client_id')->constrained(); // Cliente que fez a compra/orçamento/ordem de serviço
            $table->foreignId('user_id')->nullable()->constrained(); // Usuário logado no sistema
            $table->foreignId('cashier_info_id')->nullable()->constrained(); // Caixa aberto no momento da venda
            $table->foreignId('order_id')->nullable()->constrained(); // Ordem associada a essa ordem. Pode ser uma ordem de garantia, por exemplo

            $table->timestamps();

            $table->foreign('seller_id')
                ->references('id')
                ->on('users');

            $table->foreign('canceled_user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['seller_id']);
			$table->dropForeign(['canceled_user_id']);
        });
        Schema::dropIfExists('orders');
    }
}
