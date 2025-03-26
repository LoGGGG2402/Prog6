<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display form to create a new teacher account.
     */
    public function createTeacher()
    {
        if (!Auth::user()->isTeacher()) {
            return redirect()->route('home')->with('error', 'Only teachers can add new teachers');
        }
        
        return view('users.create-teacher');
    }

    /**
     * Store a newly created teacher account.
     */
    public function storeTeacher(Request $request)
    {
        if (!Auth::user()->isTeacher()) {
            return redirect()->route('home')->with('error', 'Only teachers can add new teachers');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s._-]+$/',
            'fullname' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_.-]+$/', // Alphanumeric, underscore, dot, hyphen only
                Rule::unique('users'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users'),
            ],
            'phone' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::create([
            'name' => strip_tags($validated['name']),
            'fullname' => strip_tags($validated['fullname']),
            'username' => strip_tags($validated['username']),
            'email' => filter_var($validated['email'], FILTER_SANITIZE_EMAIL),
            'phone' => strip_tags($validated['phone']),
            'password' => Hash::make($validated['password']),
            'role' => 'teacher',
        ]);
        
        return redirect()->route('students.index')->with('success', 'Teacher account created successfully!');
    }

    /**
     * Display form to create a new student account.
     */
    public function createStudent()
    {
        if (!Auth::user()->isTeacher()) {
            return redirect()->route('home')->with('error', 'Only teachers can add new students');
        }
        
        return view('users.create-student');
    }

    /**
     * Store a newly created student account.
     */
    public function storeStudent(Request $request)
    {
        if (!Auth::user()->isTeacher()) {
            return redirect()->route('home')->with('error', 'Only teachers can add new students');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s._-]+$/',
            'fullname' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_.-]+$/', // Alphanumeric, underscore, dot, hyphen only
                Rule::unique('users'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users'),
            ],
            'phone' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::create([
            'name' => strip_tags($validated['name']),
            'fullname' => strip_tags($validated['fullname']),
            'username' => strip_tags($validated['username']),
            'email' => filter_var($validated['email'], FILTER_SANITIZE_EMAIL),
            'phone' => strip_tags($validated['phone']),
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);
        
        return redirect()->route('students.index')->with('success', 'Student account created successfully!');
    }
}
