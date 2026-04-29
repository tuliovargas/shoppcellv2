<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceInfo extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'issue',
        'technical_report',
        'due_date',
        'user_id',
        'os_status',
        'order_product_id',
        'brand_id',
        'brand_model_id',
        'checklist'
    ];

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function brandModel()
    {
        return $this->belongsTo(BrandModel::class);
    }

    public function tecnician()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatus(){
        $allStatus = [
            'waiting_approval' => 'Aguardando Aprovação do Cliente', // Aguardando aprovação do cliente || tela::PDV || listagem de ordens
            'approved' => 'Aprovado pelo Cliente', // Cliente liberou para que faça a manutenção || tela::PDV
            'waiting_stock' => 'Aguardando Peça', //Aguardando peça do fornecedor || tela::Manutençao
            'maintenance' => 'Em Manutenção', //Em manutenção || tela::Manutençao
            'no_maintenance' => 'Sem Concerto', //sem concerto || tela::Manutençao || listagem de ordens
            'fixed' => 'Finalizado/Consertado', //finalizado/consertado || tela::Manutençao || listagem de ordens
            'finished' => 'Enviado para o Caixa', // enviado para recebimento no Caixa || tela::PDV só vai aparecer após o liberação do técnico
            'cancelled' => 'Cancelado'
        ];

        return $allStatus[$this->os_status] ?? 'Indeterminado';
    }

    public function getProduct(){
        $brand = $this->brand;
        $brandModel = $this->brandModel;

        return ($brand ? $brand->name : '') . ' ' . ($brandModel ? $brandModel->name : '');
    }
}
