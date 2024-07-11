<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItems extends Model
{
    use HasFactory, SoftDeletes;    

    
    protected $dates = ['deleted_at'];
    
    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'category_id',
        'design_id',
        'quantity',
        'rate_per',
        'discount_amount',
        'discount_percentage',
        'sub_total',
        'dimension',
        'total',
    ];

    

    public function Order()
    {
        return $this->belongsTo(Orders::class);
    }

    public function catagories(){
        return $this->belongsTo(Categories::class, 'category_id')->withTrashed();
    }

    public function design(){
        return $this->belongsTo(Designs::class, 'design_id')->withTrashed();
    }



}
 