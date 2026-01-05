<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function redirectTo()
    {
        $user = Auth::user();
        if ($user->hasRole(['admin', 'author'])) {
            return route('backend.dashboard');
        }
        return route('frontend.dashboard');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->hasRole(['admin', 'author'])) {
            return redirect()->route('backend.dashboard')->with('success', 'Welcome to Admin Dashboard!');
        }
        return redirect()->route('frontend.dashboard')->with('success', 'Welcome to your Dashboard!');
    }
}
