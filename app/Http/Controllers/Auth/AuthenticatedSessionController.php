<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Define role-based redirection
        $user = auth()->user();
        $redirectRoutes = [
            'admin'      => 'admin.dashboard',
            'garagetouchpanel' => 'touch.switches',
            'fronttouchpanel' => 'touch.switches',
            'limited'    => 'limited.dashboard',
            'default'    => 'dashboard', // Fallback for normal users
        ];

    $route = $redirectRoutes[$user->role] ?? 'dashboard'; // Default to 'dashboard' if role is unknown

    return redirect()->intended(route($route, absolute: false));
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
