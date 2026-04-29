<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('postcode', 8)->nullable();
            $table->string('street', 120)->nullable();
            $table->string('number', 15)->nullable();
            $table->string('complement', 30)->nullable();
            $table->string('neighborhood', 30)->nullable();
            $table->string('city', 30)->nullable();
            $table->string('state', 2)->nullable();
            $table->string('owner_type');
            $table->unsignedBigInteger('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
