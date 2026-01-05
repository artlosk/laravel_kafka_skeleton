<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $password
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function posts()
    {
        return $this->hasMany(\App\Models\Post::class);
    }

    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function worksheets()
    {
        return $this->hasMany(Worksheet::class);
    }

    public function worksheetItems()
    {
        return $this->hasMany(WorksheetItem::class);
    }

    public function adminlte_profile_url()
    {
        return route('backend.profile');
    }

    public function adminlte_image()
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function adminlte_desc()
    {
        return $this->roles->pluck('name')->implode(', ') ?: 'Пользователь';
    }

    public function getLastApiToken()
    {
        return $this->tokens()->latest()->first();
    }

    public function createApiToken($name = 'Admin Token', $expiresAt = null)
    {
        $this->tokens()->delete();

        return $this->createToken($name, ['*'], $expiresAt);
    }

    public function hasApiToken()
    {
        return $this->tokens()->exists();
    }

    public function hasValidApiToken()
    {
        $token = $this->getLastApiToken();
        return $token && !$token->expires_at || $token->expires_at > now();
    }

    public function getApiTokenInfo()
    {
        $token = $this->getLastApiToken();
        if (!$token) {
            return null;
        }

        return [
            'token' => $token->token,
            'created_at' => $token->created_at,
            'expires_at' => $token->expires_at,
            'is_expired' => $token->expires_at && $token->expires_at < now(),
            'days_until_expiry' => $token->expires_at ? now()->diffInDays($token->expires_at, false) : null,
        ];
    }
}
