<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 50)->nullable();
            $table->string('name', 50);
            $table->decimal('price', 8, 2);
            $table->decimal('cost', 8, 2)->nullable();
            $table->decimal('score', 3, 2)->nullable();
            $table->string('observation')->nullable();
            $table->unsignedInteger('minimum_stock')->nullable();
            $table->boolean('can_discount')->default(0);
            $table->decimal('discount_percentage')->nullable();
            $table->boolean('can_commission')->default(0);
            $table->decimal('commission_percentage')->default(0.00);
            $table->decimal('technician_commission_percentage')->default(0.00);
            $table->unsignedInteger('quantity_in_stock')->default(0);
            $table->boolean('is_new')->default(0);
            $table->boolean('is_active')->default(0);
            $table->unsignedInteger('days_warranty');
            $table->enum('type', ['un', 'sv', 'kg', 'other']); //sv é serviço

            $table->foreignId('user_id')->constrained(); //Usuário que criou o produto
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('brand_model_id')->nullable()->constrained();

            $table->softDeletes();
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
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['brand_id']);
        });
        Schema::dropIfExists('products');
    }
}
