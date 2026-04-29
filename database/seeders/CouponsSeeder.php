<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('coupons')->insert([
            [
                'user_id' => 1,
                'name' => 'natal2020',
                'start_date' => '2021-01-01',
                'end_date' => '2022-02-10',
                'quantity' => 50,
                'value' => 20,
                'order_id' => null
            ],
            [
                'user_id' => 1,
                'name' => 'teclado10',
                'start_date' => '2021-01-01',
                'end_date' => '2021-02-10',
                'quantity' => 50,
                'value' => 10,
                'order_id' => null
            ],
            [
                'user_id' => 1,
                'name' => 'desconto15',
                'start_date' => '2021-01-01',
                'end_date' => '2021-02-10',
                'quantity' => 50,
                'value' => 15,
                'order_id' => null
            ],
        ]);
    }
}
