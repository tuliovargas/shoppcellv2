<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_models', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('photo_url')->nullable(); // cadastrar arquivos na tabela uploads
            $table->boolean('status')->default(1); // listar produtos de ordem de serviço
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
        Schema::dropIfExists('brand_models');
    }
}
