<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashierInfoFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashier_info_files', function (Blueprint $table) {
            $table->id();
            $table->string('mime_type');
            $table->string('name');
            $table->string('path');
            $table->boolean('is_open')->default(true);

            $table->foreignId('cashier_info_id')->constrained();

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
        Schema::dropIfExists('cashier_info_files');
    }
}
