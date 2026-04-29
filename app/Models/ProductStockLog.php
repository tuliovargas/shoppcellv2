<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStockLog extends Model
{

	protected $table = 'product_stock_logs';
	protected $fillable = [
		'new',
		'old',
		'product_id',
		'user_id',
	];

	protected $casts = [
		'new' => 'integer',
		'old' => 'integer',
	];


	function user(){
		return $this->belongsTo(User::class)->withTrashed();
	}

}
