<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $cred = $request->validate(['email' => 'required|email', 'password' => 'required']);
        if (! Auth::attempt($cred, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email or password is incorrect.'])->onlyInput('email');
        }
        $request->session()->regenerate();

        return redirect()->intended(route('account'));
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $user = User::create($data + ['role' => 'customer']);
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('account'))->with('ok', 'Welcome, '.$user->name.'!');
    }

    public function logout(Request $request)
    {
        $staff = $request->user()?->isStaff();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($staff ? 'admin.login' : 'home');
    }

    public function index(Request $request)
    {
        return view('store.account', [
            'user' => $request->user(),
            'orders' => $request->user()->orders()->withCount('items')->latest()->get(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:80',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);
        if (empty($data['password'])) unset($data['password']);
        $request->user()->update($data);

        return back()->with('ok', 'Your details were saved.');
    }

    public function order(Request $request, string $number)
    {
        $order = $request->user()->orders()->with('items', 'events')->where('number', $number)->firstOrFail();

        return view('store.track', ['order' => $order, 'searched' => true, 'fromAccount' => true]);
    }
}
