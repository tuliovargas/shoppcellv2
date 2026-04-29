<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'subtotal',
        'discount',
        'total',
        'status',
        'user_id',
        'note',
        'delivery_date',
        'seller_id',
        'canceled_user_id',
        'canceled_at',
        'cancellation_observation',
        'is_warranty',
        'coupon_id',
        'cashier_info_id',
        'order_id',
        'created_at'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function cashierInfo()
    {
        return $this->belongsTo(CashierInfo::class);
    }

    public function cashier()
    {
        return $this->hasMany(Cashier::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function uploads()
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($this->created_at)->format('d/m/Y');
    }

    public function getStatus(){
        $allStatus = [
            'is_request' => 'Orçamento', // O produto é um pedido para a compra pelo estabelecimento
            'waiting_approval' => 'Aguardando Aprovação do Cliente', //O produto é um item de manutenção como consertar computador e está aguardando o cliente aprovar o conserto
            'approved' => 'Aprovado pelo Cliente', //O produto é um item de manutenção como consertar computador e está aprovado o inicio do conserto
            'waiting_product' => 'Aguardando Produto', //O produto é um item de manutenção como consertar computador, mas ainda não tem a peça necessária no estoque
            'maintenance' => 'Em Manutenção', //O produto é um item de manutenção como consertar computador e está no processo de manutenção
            'waiting_payment' => 'Aguardando Pagamento', //Produto ou serviço concluído que está aguardando pagamento do cliente
            'concluded' => 'Concluído', //Compra de produto ou serviço concluído
            'canceled' => 'Cancelado', //Compra de produto ou serviço cancelado
            'returned' => 'Devolvido', //Compra de produto devolvido ou valor de serviço devolvido
            'waiting_maintenance' => 'Aguardando Manutenção', // aguardando manutenção
            'partially_returned' => 'Parcialmente Devolvido' // parcialmente devolvida
        ];

        return $allStatus[$this->status] ?? 'Indeterminado';
    }
}
