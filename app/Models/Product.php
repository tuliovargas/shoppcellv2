<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'barcode',
		'name',
		'price',
		'cost',
		'observation',
		'minimum_stock',
		'can_discount',
		'discount_percentage',
		'can_commission',
		'commission_percentage',
		'technician_commission_percentage',
		'quantity_in_stock',
		'is_new',
		'is_active',
		'days_warranty',
		'type',
		'user_id',
		'brand_id',
		'brand_model_id',
	];

	protected $casts = [
		'can_commission' => 'integer',
		'can_discount' => 'integer',
		'is_new' => 'integer',
		'is_active' => 'integer',
		'price' => 'decimal:2',
	];

	public function categories()
	{
		return $this->belongsToMany(Category::class);
	}

	public function orders()
	{
		return $this->belongsToMany(Order::class, 'order_products')->withPivot('price', 'discount', 'amount');
	}

	public function orderProducts()
	{
		return $this->hasMany(OrderProduct::class);
	}

	public function byProducts()
	{
		return $this->hasMany(OrderByproduct::class);
	}

	public function stocks()
	{
		return $this->belongsToMany(Stock::class)->withPivot('quantity', 'price');
	}

	public function brand()
	{
		return $this->belongsTo(Brand::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class)->withTrashed();
	}

	public function checklists()
	{
		return $this->belongsToMany(Checklist::class);
	}

	public function brandModel()
	{
		return $this->belongsTo(BrandModel::class);
	}

	public function logs()
	{
		return $this->hasMany(ProductLog::class)->latest();
	}

	public function stockLogs()
	{
		return $this->hasMany(ProductStockLog::class)->latest();
	}
}
