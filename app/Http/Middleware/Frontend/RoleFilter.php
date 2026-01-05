<?php

namespace App\Http\Middleware\Frontend;

use Illuminate\Support\Facades\Auth;
use JeroenNoten\LaravelAdminLTE\Menu\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function transform($item)
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if (isset($item['admin_only']) && $item['admin_only'] === true) {
            if (!$user->hasRole('admin')) {
                return false;
            }
        }

        if (isset($item['roles'])) {
            if (!$user->hasAnyRole($item['roles'])) {
                return false;
            }
        }

        if (isset($item['permissions'])) {
            $permissions = is_array($item['permissions']) ? $item['permissions'] : [$item['permissions']];

            $hasPermission = false;
            foreach ($permissions as $permission) {
                if ($user->can($permission)) {
                    $hasPermission = true;
                    break;
                }
            }

            if (!$hasPermission) {
                return false;
            }
        }

        if (isset($item['submenu'])) {
            $item['submenu'] = array_filter($item['submenu'], function ($subItem) {
                return $this->transform($subItem) !== false;
            });

            if (empty($item['submenu'])) {
                return false;
            }
        }

        return $item;
    }
}
