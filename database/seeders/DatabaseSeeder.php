<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ConfigurationSeeder::class,
            RolesSeeder::class,
            UserSeeder::class,
            ClientSeeder::class,
            CategoriesSeeder::class,
            PaymentMethodsSeeder::class,
            CouponsSeeder::class,
            BrandsSeeder::class,
            ProductsSeeder::class,
            ExpenseTypesSeeder::class,
            SuppliersSeeder::class,
            ExpensesSeeder::class,
            TaxInstallmentsSeeder::class,
            BanksSeeder::class,
            StockSeeder::class,
            ChecklistsSeeder::class,
            BrandModelsSeeder::class,
            CategoryProductSeeder::class
        ]);
    }
}
