<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\SecureFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // ...existing code...

    /**
     * Update the specified user's profile.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => [
                'nullable',
                'file',
                'max:2048', // 2MB max
                new SecureFile(['image']),
            ],
            'avatar_url' => 'nullable|url',
            'password' => 'nullable|string|min:8|confirmed',
        ];
        
        // Add teacher-specific rules if applicable
        if (auth()->user()->isTeacher() && $user->isStudent()) {
            $rules['username'] = 'required|string|max:255|unique:users,username,' . $user->id;
            $rules['fullname'] = 'required|string|max:255';
        }
        
        $validated = $request->validate($rules);
        
        // Update basic info
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;
        
        // Update username and fullname if teacher is editing a student
        if (auth()->user()->isTeacher() && $user->isStudent()) {
            $user->username = $validated['username'];
            $user->fullname = $validated['fullname'];
        }
        
        // Process avatar
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store(
                Config::get('filesystems.uploads.avatars'),
                'public'
            );
            
            $user->avatar = $avatarPath;
            $user->avatar_url = null; // Clear avatar URL if setting a file
        } elseif ($request->filled('avatar_url')) {
            $user->avatar_url = $validated['avatar_url'];
            
            // Delete old avatar file if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $user->avatar = null;
        }
        
        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }
        
        $user->save();
        
        return redirect()->route('profile.show', $user)->with('message', 'Profile updated successfully!');
    }

    // ...existing code...
}
