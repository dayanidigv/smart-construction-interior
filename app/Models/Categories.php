<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'type',
    ];

    public function orderItems(){
        return $this->hasMany(OrderItems::class);
    }

    public function parentCategory()
    {
        return $this->belongsTo(Categories::class, 'parent_id')->withTrashed();
    }

    public function subCategories()
    {
        return $this->hasMany(Categories::class, 'parent_id');
    }

    public function subCategorieswithTrashed()
{
    return $this->hasMany(Categories::class, 'parent_id')->withTrashed();
}
}
 