<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_methods')->insert([
            ['name' => 'Cartão crédito'],
            ['name' => 'Cartão débito'],
            ['name' => 'Dinheiro'],
            ['name' => 'Pix'],
            ['name' => 'Cheque'],
            ['name' => 'Boleto'],
        ]);
    }
}
