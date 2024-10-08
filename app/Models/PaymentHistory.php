<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentHistory extends Model
{
    use HasFactory, SoftDeletes;

    
    protected $dates = ['deleted_at'];
    
    protected $table = 'payment_history';

    protected $fillable = [
        'user_id',
        'amount',
        'payment_method',
        'payment_date',
    ];

}
