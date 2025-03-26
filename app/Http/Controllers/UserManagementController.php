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
            'name' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'fullname' => $validated['fullname'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
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
            'name' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'fullname' => $validated['fullname'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);
        
        return redirect()->route('students.index')->with('success', 'Student account created successfully!');
    }
}
