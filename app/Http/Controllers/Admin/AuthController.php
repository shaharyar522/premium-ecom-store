<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function form(Request $request)
    {
        if ($request->user()?->isStaff()) return redirect()->route('admin.dashboard');

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $cred = $request->validate(['email' => 'required|email', 'password' => 'required']);
        if (! Auth::attempt($cred, $request->boolean('remember')) || ! Auth::user()->isStaff()) {
            Auth::logout();

            return back()->withErrors(['email' => 'These details do not match a staff account.'])->onlyInput('email');
        }
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
