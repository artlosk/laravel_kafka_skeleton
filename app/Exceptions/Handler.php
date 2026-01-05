<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('admin/*') && !($e instanceof \Illuminate\Auth\AuthenticationException)) {
                if ($e instanceof ValidationException) {
                    return redirect()->back()->withErrors($e->errors())->withInput();
                }
                return redirect()->back()->with('error', __('messages.operation_failed'));
            }
        });
    }
}
