<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        
        return view('auth.login');
    }

    /**
     * Handle login attempt
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'username' => 'Invalid username or password',
        ])->withInput($request->except('password'));
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request, User $user)
    {
        // Check if current user is the profile owner or a teacher
        if (Auth::id() !== $user->id && !Auth::user()->isTeacher()) {
            return back()->with('error', "You don't have permission to update this profile");
        }

        // If a student is trying to update another student's profile
        if (Auth::user()->isStudent() && $user->id !== Auth::id()) {
            return back()->with('error', "You don't have permission to update this profile");
        }

        // Validate the input
        $rules = [
            'email' => 'required|email',
            'phone' => 'required',
        ];

        // For teacher updating student information
        if (Auth::user()->isTeacher() && $user->isStudent()) {
            $rules['username'] = [
                'required',
                Rule::unique('users')->ignore($user->id),
            ];
            $rules['fullname'] = 'required';
        }

        // Password change is optional
        if ($request->filled('password')) {
            $rules['password'] = 'required|min:6|confirmed';
        }

        $validated = $request->validate($rules);

        // Update basic user data
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];

        // Update username and fullname if teacher is updating student
        if (Auth::user()->isTeacher() && $user->isStudent()) {
            $user->username = $validated['username'];
            $user->fullname = $validated['fullname'];
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = bcrypt($validated['password']);
        }

        // Handle avatar upload - keep this public since it needs to be directly accessible
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store(
                Config::get('filesystems.uploads.avatars'),
                'public'
            );
            $user->avatar = '/storage/' . $path;
        } elseif ($request->filled('avatar_url')) {
            $user->avatar = $request->input('avatar_url');
        }

        $user->save();

        return back()->with('message', 'Profile updated successfully!');
    }
}
