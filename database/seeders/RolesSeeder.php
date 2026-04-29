<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'use pos']);
        Permission::create(['name' => 'use cashier']);
        Permission::create(['name' => 'use maintenance']);

        Permission::create(['name' => 'register']);
        Permission::create(['name' => 'register user']);
        Permission::create(['name' => 'register client']);
        Permission::create(['name' => 'register category']);
        Permission::create(['name' => 'register product']);
        Permission::create(['name' => 'register order']);
        Permission::create(['name' => 'register payment method']);
        Permission::create(['name' => 'register supplier']);
        Permission::create(['name' => 'register model']);
        Permission::create(['name' => 'register promotions']);

        Permission::create(['name' => 'use report']);

        Permission::create(['name' => 'update config']);
        Permission::create(['name' => 'update system config']);
        Permission::create(['name' => 'update installment config']);
        Permission::create(['name' => 'update taxes config']);

        $role = Role::create(['name' => 'caixa'])->givePermissionTo([
            'use cashier', 
            'register',
            'register product',
            'register promotions'
        ]);
        $role = Role::create(['name' => 'vendedor'])->givePermissionTo(['use pos', 'register order']);
        $role = Role::create(['name' => 'técnico'])->givePermissionTo('use maintenance');
        $role = Role::create(['name' => 'admin'])->givePermissionTo(Permission::all());
    }
}
