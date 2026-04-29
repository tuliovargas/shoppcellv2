<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChecklistsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('checklists')->insert([
            ['name' => 'Liga/Não Liga'],
            ['name' => 'Possui Aranhados'],
            ['name' => 'Serial'],
			['name' => 'Estado do Aparelho'],
			['name' => 'Chip/ Cartão Memória'],
			['name' => 'Carregador'],
			['name' => 'Aparelho com sinal de umidade'],
			['name' => 'Display e touch'],
        ]);
    }
}
