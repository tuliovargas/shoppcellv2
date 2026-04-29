<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stocks')->insert([
            [
                'type' => 'in',
                'purchase_invoice' => 'asdasdasd',
                'supplier_id' => 1,
                'payment_method_id' => 1,
            ],
        ]);

        DB::table('product_stock')->insert([
            [
                'product_id' => 1,
                'stock_id' => 1,
                'quantity' => 10,
                'purchase_price' => 1200,
            ],
        ]);

        DB::table('stocks')->insert([
            [
                'type' => 'in',
                'purchase_invoice' => 'ewerxcvdf',
                'supplier_id' => 1,
                'payment_method_id' => 1,
            ],
        ]);

        DB::table('product_stock')->insert([
            [
                'product_id' => 2,
                'stock_id' => 2,
                'quantity' => 20,
                'purchase_price' => 1400,
            ],
        ]);
    }
}
