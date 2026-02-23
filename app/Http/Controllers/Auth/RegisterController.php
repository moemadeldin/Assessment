<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\CreateUserAction;
use App\Http\Requests\Auth\StoreUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final readonly class RegisterController
{
    public function create(): View
    {
        return view('register');
    }

    public function store(StoreUserRequest $request, CreateUserAction $action): RedirectResponse
    {
        $user = $action->execute($request->validated());

        Auth::login($user);
        session()->regenerate();

        return to_route('test')
            ->with('success', 'Registration successful.');
    }
}