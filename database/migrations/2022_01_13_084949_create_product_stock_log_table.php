<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStockLogTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_stock_logs', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('product_id');
			$table->foreign('product_id')->on('products')->references('id')->onDelete('restrict');
			$table->unsignedBigInteger('user_id');
			$table->foreign('user_id')->on('users')->references('id')->onDelete('restrict');
			$table->integer('old')->nullable();
			$table->integer('new')->nullable();
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
		Schema::dropIfExists('product_stock_logs');
	}
}
