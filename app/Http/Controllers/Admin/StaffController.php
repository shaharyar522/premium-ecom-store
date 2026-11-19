<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    public function index()
    {
        return view('admin.staff.index', ['staff' => User::whereIn('role', ['admin', 'staff'])->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:150',
            'password' => ['required', Password::min(8)],
            'role' => 'required|in:admin,staff',
        ]);
        $user = User::firstOrNew(['email' => strtolower($data['email'])]);
        $user->fill($data)->save();

        return back()->with('ok', "{$user->name} can now sign in to the dashboard.");
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 422, 'You cannot remove yourself.');
        $user->update(['role' => 'customer']);

        return back()->with('ok', "{$user->name} no longer has dashboard access.");
    }
}
