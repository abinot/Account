<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMeta extends Model
{
    /** @use HasFactory<\Database\Factories\UserMetaFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id', 'key', 'value'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public static function findUserIdByKeyValue(string $key, string $value)
    {
        $row = self::where('key', $key)->where('value', $value)->first();
        return $row ? $row->user_id : null;
    }
}
