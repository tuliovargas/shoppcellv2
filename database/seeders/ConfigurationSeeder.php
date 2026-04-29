<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configurations')->truncate();

        DB::table('configurations')->insert([
            ['key' => 'company_name', 'value' => 'Shop Cell'],
            ['key' => 'email', 'value' => 'shoppcelloficial@gmail.com'],
            ['key' => 'instagram', 'value' => '@shoppceelloficial'],
            ['key' => 'logo', 'value' => '',],
            ['key' => 'cnpj', 'value' => '40.736.644/0001-07'],
            ['key' => 'cellphone', 'value' => '(37) 3226-1750'], //colocar icones do whatsapp
            ['key' => 'address', 'value' => 'R. Cel. Martinho Ferreira do Amaral,232,Centro, Nova Serrana - MG,'],
            ['key' => 'bussiness_hours', 'value' => 'Seg a Sex das 8:00am as 19:00h e Sáb. das 8am as 13h'],
            ['key' => 'easy_ddd', 'value' => '5537'],
            ['key' => 'easy_postcode', 'value' => '35520-122'],
            ['key' => 'cupom_text', 'value' => ''],
            ['key' => 'warranty_text', 'value' => 'Este comprovante é válido como garantia de sua venda, guarde-o.'],
            ['key' => 'orcamento_text', 'value' => 'Orçamento válido por 7 dias a partir da data no cabeçalho deste documento.'],
            ['key' => 'secret_word', 'value' => 'vendas2021'],
            ['key' => 'secret_word_for_old_orders', 'value' => 'vendas2021'],
            ['key' => 'budget', 'value' => ''],
            ['key' => 'ddd', 'value' => ''],
            ['key' => 'cep_default', 'value' => ''],
            ['key' => 'maintenance_text', 'value' => 'A Shopp Cell Celulares não se responsabiliza por qualquer tipo de perda de software e arquivos pessoais, caso tenha que formatar ou resetar o equipamento. O cliente declara que está ciente dos riscos de não ter efetuado o backup do seu equipamento. Após a conclusão do reparo, o cliente terá um prazo máximo de 90 (noventa) dias para realizar a retirada do equipamento na loja. A partir desta data, o produto será descartado para lixo eletrônico. É imprescindível a apresentação deste na entrega do equipamento. No que se refere ao reparo, somente será efetuado conforme a descrição do defeito relatado/indicado pelo cliente nesta ordem de serviço, para que dessa forma não haja quaisquer reclamações posteriores sobre demais defeitos, e outras causas.'],
            ['key' => 'qrcode_tip', 'value' => ''],
            ['key' => 'msg_week-buyers', 'value' => 'Oi {user_firstname}, gostaria de sabe se esta gostando do {produto_firstname}? Se precisar de algum suporte basta me enviar uma mensagem ou vir aqui em nossa loja. Informo também que amanha estaremos com uma promcção de películas 3d.'],
            ['key' => 'msg_birthdays', 'value' => 'Feliz aniversário {user_firstname}! Como presente, vou te dar um cupom de desconto de 5% em qualquer produto na loja. Esse cupom tem validade de 7 dias.'],
            ['key' => 'msg_detached', 'value' => '{user_firstname}, gostaria de informar que nesse sábado a loja não estará aberta.'],
        ]);
    }
}