<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuppliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('suppliers')->insert([
            [
                'name' => 'Empresa 1',
                'cnpj' => '9999999999999',
            ],
            [
                'name' => 'Empresa 2',
                'cnpj' => '8888888888888',
            ],
            [
                'name' => 'Empresa 3',
                'cnpj' => '7777777777777',
            ],
        ]);
    }
}
