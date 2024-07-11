<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaborCategory extends Model
{
    use HasFactory, SoftDeletes;

    
    protected $dates = ['deleted_at'];
    
    protected $table = 'labor_categories';

    protected $fillable = [
        'name',
        'description',
    ];
    
}
