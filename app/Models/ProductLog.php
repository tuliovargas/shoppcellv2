<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductLog extends Model
{

	protected $table = 'product_logs';
	protected $fillable = [
		'new',
		'old',
		'product_id',
		'user_id',
	];

	protected $casts = [
		'new' => 'array',
		'old' => 'array',
	];


	function user(){
		return $this->belongsTo(User::class)->withTrashed();
	}

}
