<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessHours extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id', 'day', 'start_time', 'end_time'
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
