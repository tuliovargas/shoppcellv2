<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_checks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45)->nullable();
            $table->string('check_number', 45);
            $table->string('type_expense', 45);
            $table->boolean('is_deposited')->default(0);
            $table->decimal('value', 8,2);
            $table->date('date_deposit');

            $table->foreignId('bank_account_id')->constrained();

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
        Schema::table('bank_checks', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
        });
        Schema::dropIfExists('bank_checks');
    }
}
