<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('social_name', 20)->nullable();
            $table->enum('gender', ['f', 'm'])->nullable();
            $table->string('photo_url')->nullable();
            $table->string('cpf', 14)->nullable()->unique();
            $table->string('rg', 30)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('cellphone', 13)->nullable();
            $table->string('phone', 12)->nullable();
            $table->string('email', 160)->nullable();
            $table->string('observation', 160)->nullable();
            $table->string('profession', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_add_id')->constrained('users');
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
        Schema::table('clients', function(Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::dropIfExists('clients');
    }
}
