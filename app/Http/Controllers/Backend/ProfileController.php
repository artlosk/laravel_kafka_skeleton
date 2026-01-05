<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpdateProfileRequest;
use App\Http\Requests\Backend\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        if ($request->isMethod('post')) {
            $validated = (new UpdateProfileRequest())->validate();

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            return redirect()->route('backend.profile')->with('success', 'Профиль успешно обновлен.');
        }

        return view('backend.profile.update', compact('user'));
    }

    public function changePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->isMethod('post')) {
            $validated = (new ChangePasswordRequest())->validate();

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return redirect()->route('backend.password')->with('success', 'Пароль успешно обновлен.');
        }

        return view('backend.profile.password');
    }

    public function generateApiToken(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $token = $user->createApiToken('Admin Profile Token');

            return redirect()->route('backend.profile')
                ->with('success', 'API токен успешно создан: ' . $token->plainTextToken);
        } catch (\Exception $e) {
            return redirect()->route('backend.profile')
                ->with('error', 'Ошибка при создании API токена: ' . $e->getMessage());
        }
    }

    public function revokeApiToken(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user->tokens()->delete();

            return redirect()->route('backend.profile')
                ->with('success', 'API токен успешно отозван');
        } catch (\Exception $e) {
            return redirect()->route('backend.profile')
                ->with('error', 'Ошибка при отзыве API токена: ' . $e->getMessage());
        }
    }
}
