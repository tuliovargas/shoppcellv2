<?php

namespace Database\Seeders;

use App\Models\Cashier;
use Illuminate\Database\Seeder;

class CashierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cashier::factory()
            ->count(50)
            ->create();
    }
}
