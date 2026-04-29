<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('expenses')->insert([
            [
                'name' => 'Compra de computador',
                'invoice' => '21312',
                'payday' => '2021-02-05',
                'value' => 2000,
                'installments' => 3,
                'observation' => 'Observação',
                'payment_method_id' => 1,
                'expense_type_id' => 4,
                'supplier_id' => 1,
                'cashier_info_id' => null
            ],
            [
                'name' => 'Aluguel Janeiro 2021',
                'invoice' => null,
                'payday' => '2021-02-05',
                'value' => 1000,
                'installments' => 1,
                'observation' => null,
                'payment_method_id' => 6,
                'expense_type_id' => 1,
                'supplier_id' => 1,
                'cashier_info_id' => null
            ],
            [
                'name' => 'Energia elétrica Janeiro 2021',
                'invoice' => null,
                'payday' => '2021-02-05',
                'value' => 100,
                'installments' => 1,
                'observation' => null,
                'payment_method_id' => 6,
                'expense_type_id' => 3,
                'supplier_id' => 1,
                'cashier_info_id' => null
            ],
        ]);
    }
}
