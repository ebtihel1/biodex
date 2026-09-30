<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthentifController extends Controller
{
    // Formulaire d'inscription
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Création du compte
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 1, 
        ]);

        return redirect()->route('login.form')->with('status', 'Your account has been created. Please sign in.');
    }

    // Formulaire de connexion
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();
        $role = $user->role ? $user->role->name : null;

        // Redirection selon le rôle
        if ($role === 'admin' || $role === 'entreprise') {
            return redirect()->route('back.home');
        }

        if ($role === 'citoyen') {
            return redirect('/biodex');
        }

        // Si aucun rôle défini
        return redirect('/biodex');
    }

    return back()->withErrors([
        'email' => 'Identifiants incorrects.',
    ]);
}


    // Déconnexion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
