<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_infos', function (Blueprint $table) {
            $table->id();
            $table->string('issue')->nullable(); //problema na ordem de serviço
            $table->string('technical_report')->nullable();; //laudo técnico de conclusão do trabalho.
            $table->string('due_date')->nullable(); // Data de entrega da manutenção
            $table->foreignId('user_id')->nullable()->constrained(); // Usuário do Técnico responsável pela manutencao
            $table->enum('os_status', [ //os = ordem de serviço / manutencao
                'waiting_approval', // Aguardando aprovação do cliente || tela::PDV || listagem de ordens
				'approved', // Cliente liberou para que faça a manutenção || tela::PDV
                'waiting_stock', //Aguardando peça do fornecedor || tela::Manutençao
                'maintenance', //Em manutenção || tela::Manutençao
                'no_maintenance', //sem concerto || tela::Manutençao || listagem de ordens
                'fixed', //finalizado/consertado || tela::Manutençao || listagem de ordens
                'finished', // enviado para recebimento no Caixa || tela::PDV só vai aparecer após o liberação do técnico
                'cancelled'
            ])->nullable();
            $table->json('checklist')->nullable(); //lista de itens do checklist preenchidos
            $table->foreignId('order_product_id')->nullable()->constrained();
            $table->foreignId('brand_id')->nullable()->constrained(); // Marca do produto
            $table->foreignId('brand_model_id')->nullable()->constrained();// Modelo do produto
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_infos');
    }
}
