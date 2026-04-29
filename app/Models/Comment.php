<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'user_id',
        'order_id',
    ];

    public function uploads()
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }

    public function attachments()
    {
        return $this->uploads();
    }
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
