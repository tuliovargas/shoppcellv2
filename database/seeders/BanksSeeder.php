<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BanksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banks')->insert([
            ['name' => 'Itaú'],
            ['name' => 'Banco do Brasil'],
            ['name' => 'Caixa Econômica Federal'],
            ['name' => 'Bradesco'],
            ['name' => 'Bradesco'],
            ['name' => 'Safra'],
            ['name' => 'Sicob'],
            ['name' => 'Citibank Brasil'],
        ]);
    }
}
