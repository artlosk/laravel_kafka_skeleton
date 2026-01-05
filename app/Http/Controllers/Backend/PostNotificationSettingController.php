<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StorePostNotificationSettingRequest;
use App\Http\Requests\Backend\UpdatePostNotificationSettingRequest;
use App\Models\PostNotificationSetting;
use App\Models\User;
use Spatie\Permission\Models\Role;

class PostNotificationSettingController extends Controller
{
    public function index()
    {
        $settings = PostNotificationSetting::latest()->paginate(15);

        return view('backend.post-notification-settings.index', compact('settings'));
    }

    public function create()
    {
        $roles = Role::all();
        $users = User::orderBy('name')->get();

        return view('backend.post-notification-settings.create', compact('roles', 'users'));
    }

    public function store(StorePostNotificationSettingRequest $request)
    {
        $validated = $request->validated();

        $setting = PostNotificationSetting::where('notify_type', $validated['notify_type'])->first();

        $data = [
            'notify_type' => $validated['notify_type'],
            'role_names' => $validated['notify_type'] === 'role' ? ($validated['role_names'] ?? null) : null,
            'user_ids' => $validated['notify_type'] === 'user' ? ($validated['user_ids'] ?? null) : null,
            'is_active' => $request->boolean('is_active', false),
        ];

        if ($setting) {
            $setting->update($data);
            $message = 'Настройка уведомления успешно обновлена';
        } else {
            PostNotificationSetting::create($data);
            $message = 'Настройка уведомления успешно создана';
        }

        return redirect()->route('backend.post-notification-settings.index')
            ->with('success', $message);
    }

    public function edit(PostNotificationSetting $postNotificationSetting)
    {
        $roles = Role::all();
        $users = User::orderBy('name')->get();

        return view('backend.post-notification-settings.edit', compact('postNotificationSetting', 'roles', 'users'));
    }

    public function update(UpdatePostNotificationSettingRequest $request, PostNotificationSetting $postNotificationSetting)
    {
        $validated = $request->validated();

        $postNotificationSetting->update([
            'notify_type' => $validated['notify_type'],
            'role_names' => $validated['notify_type'] === 'role' ? ($validated['role_names'] ?? null) : null,
            'user_ids' => $validated['notify_type'] === 'user' ? ($validated['user_ids'] ?? null) : null,
            'is_active' => $request->boolean('is_active', false),
        ]);

        return redirect()->route('backend.post-notification-settings.index')
            ->with('success', 'Настройка уведомления успешно обновлена');
    }

    public function destroy(PostNotificationSetting $postNotificationSetting)
    {
        $postNotificationSetting->delete();

        return redirect()->route('backend.post-notification-settings.index')
            ->with('success', 'Настройка уведомления успешно удалена');
    }

    public function toggleActive(PostNotificationSetting $postNotificationSetting)
    {
        $postNotificationSetting->update([
            'is_active' => !$postNotificationSetting->is_active,
        ]);

        $status = $postNotificationSetting->is_active ? 'активирована' : 'деактивирована';

        return back()->with('success', "Настройка успешно {$status}");
    }
}

