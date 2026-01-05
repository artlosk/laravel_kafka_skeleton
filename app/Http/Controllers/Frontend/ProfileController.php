<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            ]);

            $user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ]);

            return redirect()->route('frontend.profile')->with('success', 'Профиль успешно обновлен.');
        }

        return view('frontend.profile.update', compact('user'));
    }

    public function changePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->getAttribute('password'))) {
                        $fail('Текущий пароль неверный.');
                    }
                }],
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user->update([
                'password' => Hash::make($request->input('password')),
            ]);

            return redirect()->route('frontend.password')->with('success', 'Пароль успешно обновлен.');
        }

        return view('frontend.profile.password');
    }

    public function generateApiToken(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $token = $user->createApiToken('User Profile Token');

            return redirect()->route('frontend.profile')
                ->with('success', 'API токен успешно создан: ' . $token->plainTextToken);
        } catch (\Exception $e) {
            return redirect()->route('frontend.profile')
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

            return redirect()->route('frontend.profile')
                ->with('success', 'API токен успешно отозван');
        } catch (\Exception $e) {
            return redirect()->route('frontend.profile')
                ->with('error', 'Ошибка при отзыве API токена: ' . $e->getMessage());
        }
    }
}
