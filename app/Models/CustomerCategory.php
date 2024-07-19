<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCategory extends Model
{
    
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    
    protected $table = 'customer_category';

    protected $fillable = [
        'name',
        'description',
    ];
    
}
