<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Labour extends Model
{
    use HasFactory, SoftDeletes;
    
    
    protected $dates = ['deleted_at'];
    protected $table = 'labors';

    protected $fillable = [
        'order_id',
        'date',
        'labor_category_id',
        'number_of_labors',
        'per_labor_amount',
        'total_amount',
    ];

    public function category()
    {
        return $this->belongsTo(LaborCategory::class,'labor_category_id');
    }
}
