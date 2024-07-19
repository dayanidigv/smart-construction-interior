<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiries extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    
    protected $table = 'enquiries';

    protected $fillable = [
        'customer_category_id',
        'description',
        'site_status',
        'type_of_work',
        'user_id',
        'creator_id',
        'customer_id',
        'status',
    ];
    
    public function user(){
        return $this->belongsTo(User::class,"user_id")->withTrashed();
    }

    public function creator(){
        return $this->belongsTo(User::class,"creator_id")->withTrashed();
    }

    public function customer(){
        return $this->belongsTo(Customers::class,"customer_id")->withTrashed();
    }

    public function customerCategory(){
        return $this->belongsTo(CustomerCategory::class,"customer_category_id")->withTrashed();
    }

}
