<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'fiscal_code' => 'nullable|string|max:16|unique:users',
            'role' => 'required|in:employee,manager,hr',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'fiscal_code' => $validated['fiscal_code'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'status' => 'pending',
        ]);

        return redirect()->route('login')
            ->with('success', 'Registrazione completata! Il tuo account è in attesa di approvazione da parte di un amministratore.');
    }
}
