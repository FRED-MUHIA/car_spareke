<?php

namespace App\Http\Controllers;

use App\Mail\SellerPendingApprovalMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
        }

        if (! Auth::user()->email_verified_at) {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'Your account is pending verification. Please wait for admin approval before logging in.'])
                ->onlyInput('email');
        }

        if (Auth::user()->isOnProbation()) {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'Your account is under probation. Please contact support for help.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->dashboardRoute());
    }

    public function dashboard(): RedirectResponse
    {
        return redirect($this->dashboardRoute());
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:40'],
            'location' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'in:seller,garage'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'location' => $data['location'],
            'role' => $data['role'] ?? 'seller',
            'password' => Hash::make($data['password']),
        ]);

        Mail::to($user->email)->send(new SellerPendingApprovalMail($user));

        return redirect()->route('login')->with('status', 'Account created. Please wait for admin verification before logging in.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function dashboardRoute(): string
    {
        return match (Auth::user()?->role) {
            'admin' => route('admin.dashboard'),
            'garage' => route('garage.dashboard'),
            default => route('seller.dashboard'),
        };
    }
}
