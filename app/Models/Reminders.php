<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminders extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at', 'reminder_time'];

    protected $table = 'reminders';

    protected $fillable = [
        'user_id ',
        'order_id ',
        'enquiry_id',
        'title',
        'description',
        "reminder_time",
        'is_completed',
        'priority',
        'category',
        'repeat',
        "notes",
    ];


    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
