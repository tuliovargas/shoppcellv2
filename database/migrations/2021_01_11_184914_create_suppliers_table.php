<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('photo_url')->nullable();
            $table->string('cnpj', 14);
            $table->string('state_registration', 12)->nullable();
            $table->string('cellphone', 11)->nullable();
            $table->string('phone', 11)->nullable();
            $table->string('responsible_person', 60)->nullable();
            $table->string('observation', 160)->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**clients copy
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}
