<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show login page
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()->withErrors(['username' => 'User not found']);
        }

        // Admin bypass: admin/admin
        if ($user->username === 'admin' && $request->password === 'admin') {
            Auth::login($user);
            return redirect()->route('dashboard');
        }

        // Normal user login with hashed password
        if (Hash::check($request->password, $user->password)) {
            Auth::login($user);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['username' => 'Invalid credentials']);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }


    // Show register form
public function showRegister()
{
    return view('auth.register');
}

// Handle registration
public function register(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string|min:4',
    ]);

    User::create([
        'username' => $request->username,
        'password' => Hash::make($request->password),
    ]);

    // Go back to login page (user remains logged out)
    return redirect()->route('login')->with('success', 'Account created! Please log in.');
}

public function createEmployeeUser(Request $request)
{
    $request->validate([
        'employee_id' => 'required|integer',
        'username' => 'required|email',
        'password' => 'required'
    ]);

    User::create([
        'employee_id' => $request->employee_id,
        'username' => $request->username,
        'email' => $request->username,
        'password' => bcrypt($request->password),
    ]);

    return response()->json(['message' => 'User created']);
}

}
