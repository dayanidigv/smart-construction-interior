<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customers extends Model
{
    use HasFactory, SoftDeletes;

    
    protected $dates = ['deleted_at'];
    protected $table = 'customers';

    protected $fillable = [
        'user_id',
        'name',
        "phone",
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getUser()
    {
        return $this->hasMany(User::class); 
    }
}
