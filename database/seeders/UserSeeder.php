<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    
        $user = User::create([ //Usuário Admin 1
            'name' => 'Thales Admin', 'email' => 'thales@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => true
        ]);
        $user->assignRole('admin');
        //fim criar usuario

        $user = User::create([ //Usuário Vendedor 2
            'name' => 'Thales Vendedor', 'email' => 'thales.vendedor@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => false, 'deleted_at' => '2021-04-19 08:37:49'
        ]);
        $user->assignRole('vendedor');
        //fim criar usuario

        $user = User::create([ //Usuário Tecnico 3
			'name' => 'Thales Técnico', 'email' => 'thales.tecnico@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => false, 'deleted_at' => '2021-04-19 08:37:49'
		]);
		$user->assignRole('técnico');
        //fim criar usuario
    
        $user = User::create([ //Usuário Caixa 4
    		'name' => 'Thales Caixa', 'email' => 'thales.caixa@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => true,
		]);
		$user->assignRole('caixa');
        //fim criar usuario

		$user = User::create([ //Usuário Vendedor 5
			'name' => 'Elias', 'email' => 'elias@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => true
		]);
        $user->assignRole('vendedor');
        //fim criar usuario

		$user = User::create([ //Usuário Vendedor 6
			'name' => 'Diego', 'email' => 'diego@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => false, 'deleted_at' => '2021-04-19 08:37:49'
		]);
        $user->assignRole('vendedor');
        //fim criar usuario

        $user = User::create([ //Usuário Vendedor 7
			'name' => 'Maria', 'email' => 'maria@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => true
		]);
        $user->assignRole('vendedor');
        //fim criar usuario

		$user = User::create([ //Usuário Vendedor 8
			'name' => 'Nathalia', 'email' => 'nathalia@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => false, 'deleted_at' => '2021-04-19 08:37:49'
		]);
        $user->assignRole('vendedor');
        //fim criar usuario

		$user = User::create([ //Usuário Vendedor 9
			'name' => 'Markele', 'email' => 'markele@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => false, 'deleted_at' => '2021-04-19 08:37:49'
		]);
        $user->assignRole('vendedor');
        //fim criar usuario

    	$user = User::create([ //Usuário Vendedor 10
			'name' => 'Luiz Gaipo', 'email' => 'luizgaipo@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => false, 'deleted_at' => '2021-04-19 08:37:49'
				]);
        $user->assignRole('vendedor');
        //fim criar usuario

		$user = User::create([ //Usuário Vendedor 11
			'name' => 'Rodrigo Técnico', 'email' => 'rodrigo@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => true
		]);
        $user->assignRole('técnico');
        //fim criar usuario

		$user = User::create([ //Usuário Vendedor 12
			'name' => 'Fabio Cardoso', 'email' => 'fabiocardoso@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => false, 'deleted_at' => '2021-04-19 08:37:49'
		]);
        $user->assignRole('vendedor');
        //fim criar usuario

		$user = User::create([ //Usuário Vendedor 13
			'name' => 'Keyla Carolina', 'email' => 'keylaarolina@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => false, 'deleted_at' => '2021-04-19 08:37:49'
		]);
        $user->assignRole('vendedor');
        //fim criar usuario

		$user = User::create([ //Usuário Vendedor 14
			'name' => 'Douglas', 'email' => 'douglas@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => false, 'deleted_at' => '2021-04-19 08:37:49'
    	]);
        $user->assignRole('vendedor');
        //fim criar usuario

		$user = User::create([ //Usuário Vendedor 15
			'name' => 'Lara', 'email' => 'lara@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => false, 'deleted_at' => '2021-04-19 08:37:49'
		]);
        $user->assignRole('vendedor');
        //fim criar usuario

        $user = User::create([ //Usuário Tecnico 16
            'name' => 'Bruno Técnico', 'email' => 'tecnico@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => false, 'deleted_at' => '2021-04-19 08:37:49'
        ]);
        $user->assignRole('técnico');
        //fim criar usuario

        $user = User::create([ //Usuário Caixa 17
            'name' => 'Daiene Caixa', 'email' => 'daiene@lojashoppcell.com.br', 'password' => bcrypt('123456'), 'is_active' => true
        ]);
        $user->assignRole('caixa');
        //fim criar usuario
    }
    }
