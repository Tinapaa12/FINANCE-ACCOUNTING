<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($request->username === 'finance_admin' && $request->password === '12345') {
            session(['auth_logged_in' => true, 'auth_username' => 'finance_admin']);
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['login' => 'Invalid username or password.'])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        session()->forget(['auth_logged_in', 'auth_username']);
        return redirect('/login');
    }
}
