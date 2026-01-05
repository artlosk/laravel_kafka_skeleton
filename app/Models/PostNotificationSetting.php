<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostNotificationSetting extends Model
{
    protected $fillable = [
        'notify_type',
        'role_names',
        'user_ids',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'role_names' => 'array',
        'user_ids' => 'array',
    ];


    public static function getUsersToNotify(): array
    {
        $users = [];
        $settings = self::where('is_active', true)->get();

        foreach ($settings as $setting) {
            if ($setting->notify_type === 'user' && !empty($setting->user_ids)) {
                $selectedUsers = User::whereIn('id', $setting->user_ids)->get();
                $users = array_merge($users, $selectedUsers->all());
            } elseif ($setting->notify_type === 'role' && !empty($setting->role_names)) {
                foreach ($setting->role_names as $roleName) {
                    $roleUsers = User::role($roleName)->get();
                    $users = array_merge($users, $roleUsers->all());
                }
            }
        }

        return collect($users)->unique('id')->values()->all();
    }

    public function getRoleNamesDisplayAttribute(): string
    {
        if (empty($this->role_names)) {
            return '-';
        }
        return implode(', ', $this->role_names);
    }

    public function getUserNamesDisplayAttribute(): string
    {
        if (empty($this->user_ids)) {
            return '-';
        }
        $users = User::whereIn('id', $this->user_ids)->pluck('name')->toArray();
        return implode(', ', $users);
    }

    public function getSelectedCountAttribute(): int
    {
        if ($this->notify_type === 'role') {
            return count($this->role_names ?? []);
        }
        return count($this->user_ids ?? []);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query)
    {
        return $query->where('notify_type', 'role');
    }

    public function scopeByUser($query)
    {
        return $query->where('notify_type', 'user');
    }
}

