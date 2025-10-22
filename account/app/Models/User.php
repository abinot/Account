<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function passwords()
    {
        return $this->hasMany(UserPassword::class);
    }

    // 🔹 گرفتن رمز با key
    public function passwordByKey(string $key)
    {
        return $this->passwords()->where('key', $key)->first();
    }

    // 🔹 گرفتن رمز با id
    public function passwordById(int $id)
    {
        return $this->passwords()->where('id', $id)->first();
    }

    // 🔹 یک متد عمومی که خودش تشخیص بده key یا id
    public function password($identifier)
    {
        if (is_numeric($identifier)) {
            return $this->passwordById((int)$identifier);
        }
        return $this->passwordByKey($identifier);
    }
    public function metas()
    {
        return $this->hasMany(UserMeta::class);
    }

}
