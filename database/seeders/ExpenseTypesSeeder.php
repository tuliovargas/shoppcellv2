<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('expense_types')->insert([
            [
                'name' => 'Aluguel',
                'type' => 'fixed',
            ],
            [
                'name' => 'Água',
                'type' => 'variable',
            ],
            [
                'name' => 'Energia elétrica',
                'type' => 'variable',
            ],
            [
                'name' => 'Fornecedor',
                'type' => 'variable',
            ],
        ]);
    }
}
