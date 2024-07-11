<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuantityUnits extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'quantity_units';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'description',
    ];

    public function unit($id)
    {
        return QuantityUnits::find($id)->withTrashed();
    }
}