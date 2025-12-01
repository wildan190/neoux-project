<?php

namespace App\Modules\Auth\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Presentation\Http\Requests\LoginRequest;
use App\Modules\Auth\Services\LoginService;
use App\Modules\Auth\Services\LogoutService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, LoginService $service)
    {
        $service->handle($request->validated());

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request, LogoutService $service)
    {
        $service->handle($request);

        return redirect('/');
    }
}
