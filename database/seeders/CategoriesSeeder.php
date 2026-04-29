<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['id' => '1', 'parent_id' => NULL, 'name' => 'Manutenção', 'photo_url' => NULL],
            ['id' => '2', 'parent_id' => NULL, 'name' => 'Ordem de Serviço', 'photo_url' => NULL],
            ['id' => '3', 'parent_id' => NULL, 'name' => 'Acessórios', 'photo_url' => NULL],
            ['id' => '4', 'parent_id' => NULL, 'name' => 'Caixas De Som', 'photo_url' => NULL],
            ['id' => '5', 'parent_id' => NULL, 'name' => 'Celulares', 'photo_url' => NULL],
            ['id' => '6', 'parent_id' => NULL, 'name' => 'Geral', 'photo_url' => NULL],
            ['id' => '7', 'parent_id' => NULL, 'name' => 'Informática', 'photo_url' => NULL],
            ['id' => '8', 'parent_id' => NULL, 'name' => 'Relógios', 'photo_url' => NULL],
            ['id' => '9', 'parent_id' => NULL, 'name' => 'Tablets', 'photo_url' => NULL],
            ['id' => '10', 'parent_id' => NULL, 'name' => 'Vídeo Games', 'photo_url' => NULL],
            ['id' => '11', 'parent_id' => NULL, 'name' => 'Som', 'photo_url' => NULL],
            ['id' => '12', 'parent_id' => '3', 'name' => 'Baterias', 'photo_url' => NULL],
            ['id' => '13', 'parent_id' => '3', 'name' => 'Cabos', 'photo_url' => NULL],
            ['id' => '14', 'parent_id' => '3', 'name' => 'Capinhas', 'photo_url' => NULL],
            ['id' => '15', 'parent_id' => '3', 'name' => 'Carregadores', 'photo_url' => NULL],
            ['id' => '16', 'parent_id' => '3', 'name' => 'Cartão Memória', 'photo_url' => NULL],
            ['id' => '17', 'parent_id' => '3', 'name' => 'Fone de Ouvido', 'photo_url' => NULL],
            ['id' => '18', 'parent_id' => '3', 'name' => 'Fontes de carregadores', 'photo_url' => NULL],
            ['id' => '19', 'parent_id' => '3', 'name' => 'Películas', 'photo_url' => NULL],
            ['id' => '20', 'parent_id' => '7', 'name' => 'Modens', 'photo_url' => NULL],
            ['id' => '21', 'parent_id' => '7', 'name' => 'Roteadores', 'photo_url' => NULL],
            ['id' => '22', 'parent_id' => '7', 'name' => 'Celulares Semi-novos', 'photo_url' => NULL],

        ]);
    }
}
