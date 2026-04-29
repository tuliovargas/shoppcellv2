<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxInstallmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tax_installments')->insert([
            [
                'interest_rates' => 0,
                'quantity' => 1,
            ],
            [
                'interest_rates' => 1,
                'quantity' => 2,
            ],
            [
                'interest_rates' => 1.5,
                'quantity' => 3,
            ],
            [
                'interest_rates' => 2,
                'quantity' => 4,
            ],
            [
                'interest_rates' => 2.5,
                'quantity' => 5,
            ],
            [
                'interest_rates' => 3,
                'quantity' => 6,
            ],
            [
                'interest_rates' => 3,
                'quantity' => 7,
            ],
            [
                'interest_rates' => 3,
                'quantity' => 8,
            ],
            [
                'interest_rates' => 3,
                'quantity' => 9,
            ],
            [
                'interest_rates' => 3,
                'quantity' => 10,
            ],
            [
                'interest_rates' => 3,
                'quantity' => 11,
            ],
            [
                'interest_rates' => 3,
                'quantity' => 12,
            ],
        ]);
    }
}
