<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->decimal('price', 8, 2)->default(0.00);
            $table->decimal('addition', 8, 2)->default(0.00);
            $table->decimal('discount', 8, 2)->default(0.00);
            $table->decimal('cost', 8, 2)->default(0.00);
            $table->decimal('profit', 8, 2)->default(0.00);
            $table->unsignedInteger('amount');
            $table->decimal('commission', 8, 2)->default(0.00);
            $table->decimal('commission_percentage', 8, 2)->default(0.00);
            $table->boolean('commission_payed')->default(false);
            $table->decimal('technician_commission', 8, 2)->default(0.00);
            $table->decimal('technician_commission_percentage', 8, 2)->default(0.00);
            $table->boolean('technician_commission_payed')->default(false);
            $table->timestamp('canceled_at')->nullable();
            $table->text('cancellation_observation')->nullable();
            
            $table->bigInteger('canceled_user_id')->unsigned()->nullable(); // Usuário logado no sistema
            
            $table->timestamps();

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
        Schema::dropIfExists('order_products');
    }
}
