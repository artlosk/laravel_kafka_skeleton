<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreUserRequest;
use App\Http\Requests\Backend\UpdateUserRequest;
use App\Services\Backend\UserFilterService;
use App\Services\Backend\UserService;
use Spatie\Permission\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        protected UserFilterService $filterService,
        protected UserService       $userService
    )
    {
        $this->middleware(['auth', 'permission:manage-users']);
    }

    public function index(Request $request): View
    {
        $users = $this->filterService->getFilteredUsers($request, self::PAGINATION_LIMIT);
        $roles = Role::orderBy('name')->get();

        return view('backend.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::all();
        return view('backend.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $user = $this->userService->createUser(
                $request->validated(),
                $request->roles
            );

            $token = $this->userService->createApiToken($user, 'Auto Generated Token');

            return redirect()->route('backend.users.index')
                ->with('success', 'Пользователь создан успешно. API токен: ' . $token->plainTextToken);
        } catch (\Exception $e) {
            return back()->with('error', __('User creation failed'));
        }
    }

    public function edit(User $user): View
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('backend.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $data = $request->validated();
            if ($request->password) {
                $data['password'] = $request->password;
            }

            $this->userService->updateUser($user, $data, $request->roles);

            return redirect()->route('backend.users.index')
                ->with('success', __('User updated successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('User update failed'));
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->userService->deleteUser($user);
            return redirect()->route('backend.users.index')
                ->with('success', __('User deleted successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('User deletion failed'));
        }
    }

    public function generateApiToken(User $user): RedirectResponse
    {
        try {
            $token = $this->userService->createApiToken($user, 'Admin Generated Token');

            return back()->with('success', 'API токен успешно сгенерирован: ' . $token->plainTextToken);
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при генерации API токена: ' . $e->getMessage());
        }
    }

    public function revokeApiToken(User $user): RedirectResponse
    {
        try {
            $this->userService->revokeApiTokens($user);

            return back()->with('success', 'API токен успешно отозван');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при отзыве API токена: ' . $e->getMessage());
        }
    }
}
