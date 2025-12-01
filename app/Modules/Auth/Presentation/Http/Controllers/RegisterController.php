<?php

namespace App\Modules\Auth\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Presentation\Http\Requests\RegisterRequest;
use App\Modules\Auth\Services\RegisterService;

class RegisterController extends Controller
{

    public function index()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request, RegisterService $service)
    {
        $service->handle($request->validated());

        return redirect(route('dashboard', absolute: false));
    }
}
