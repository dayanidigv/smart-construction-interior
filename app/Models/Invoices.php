<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoices extends Model
{
    use HasFactory, SoftDeletes;

    
    protected $dates = ['deleted_at'];
    protected $table = 'invoices';

    protected $fillable = [
        'user_id',
        'order_id',
        'customer_id',
        'invoice_number',
        'discount_amount',
        'discount_percentage',
        'sub_total_amount',
        'total_amount',
        'advance_pay_amount',
        'balance_amount',
        'payment_status',
        'payment_method',
        'payment_history',
        'terms_and_conditions',
        'created_date',
        'due_date',
    ];
}
 