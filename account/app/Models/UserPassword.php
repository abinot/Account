<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPassword extends Model
{
    use HasFactory;

    // اجازه می‌دهیم فیلدهای مهم به صورت mass assignable باشند
    protected $fillable = [
        'user_id',
        'name',
        'key',
        'hint',
        'type',
        'value',
        'value2',
        'encryption_type',
        'usage_count',
        'attempt_count',
        'last_used_at',
        'expired_at',
        'is_active',
        'delete_type',
        'note',
    ];

    protected $hidden = [
        'type',
        'value',
        'value2',
        'encryption_type',
        'usage_count',
        'attempt_count',
        'last_used_at',
        'expired_at',
        'is_active',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    // رابطه معکوس با User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
