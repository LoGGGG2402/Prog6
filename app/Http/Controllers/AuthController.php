<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\FileValidator;
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
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
        ];

        // For teacher updating student information
        if (Auth::user()->isTeacher() && $user->isStudent()) {
            $rules['username'] = [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_.-]+$/', // Alphanumeric, underscore, dot, hyphen only
                Rule::unique('users')->ignore($user->id),
            ];
            $rules['fullname'] = 'required|string|max:255';
        }

        // Password change is optional
        if ($request->filled('password')) {
            $rules['password'] = 'required|min:8|confirmed';
        }
        
        // Avatar validation if provided
        if ($request->hasFile('avatar')) {
            $rules['avatar'] = [
                'file',
                'max:2048', // 2MB max
                'mimes:jpeg,png,gif',
            ];
        } elseif ($request->filled('avatar_url')) {
            $rules['avatar_url'] = [
                'url',
                'max:255',
            ];
        }

        $validated = $request->validate($rules);

        // Update basic user data (sanitized)
        $user->email = filter_var($validated['email'], FILTER_SANITIZE_EMAIL);
        $user->phone = strip_tags($validated['phone']);

        // Update username and fullname if teacher is updating student
        if (Auth::user()->isTeacher() && $user->isStudent()) {
            $user->username = strip_tags($validated['username']);
            $user->fullname = strip_tags($validated['fullname']);
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = bcrypt($validated['password']);
        }

        // Handle avatar upload - keep this public since it needs to be directly accessible
        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists and is not a URL
            if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL) && strpos($user->avatar, '/storage/') === 0) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            // Sanitize filename
            $originalName = $request->file('avatar')->getClientOriginalName();
            $fileName = FileValidator::sanitizeFilename($originalName);
            
            $path = $request->file('avatar')->store(
                Config::get('filesystems.uploads.avatars'),
                'public'
            );
            
            // Validate stored path
            if (!$path || strpos($path, '..') !== false) {
                return redirect()->back()->with('error', 'Invalid file path detected.');
            }
            
            $user->avatar = '/storage/' . $path;
        } elseif ($request->filled('avatar_url')) {
            // Validate and sanitize URL
            $avatarUrl = $validated['avatar_url'];
            if (filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
                // Only allow specific domains (example)
                $allowedDomains = ['gravatar.com', 'secure.gravatar.com', 'avatars.githubusercontent.com'];
                $urlParts = parse_url($avatarUrl);
                
                if (isset($urlParts['host']) && in_array($urlParts['host'], $allowedDomains)) {
                    $user->avatar = $avatarUrl;
                } else {
                    return redirect()->back()->with('error', 'Avatar URL from this domain is not allowed');
                }
            } else {
                return redirect()->back()->with('error', 'Invalid avatar URL');
            }
        }

        $user->save();

        return back()->with('message', 'Profile updated successfully!');
    }
}
