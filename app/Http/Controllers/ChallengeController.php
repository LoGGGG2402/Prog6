<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChallengeController extends Controller
{
    /**
     * Display a listing of challenges.
     */
    public function index(Request $request)
    {
        $teacherId = $request->query('teacher_id', 0);
        $teachers = User::teachers()->get();
        
        if ($teacherId > 0) {
            $challenges = Challenge::with('teacher')
                ->where('teacher_id', $teacherId)
                ->get();
        } else {
            $challenges = Challenge::with('teacher')->get();
        }
        
        // For students, check which challenges they've already solved
        $answeredChallenges = [];
        if (Auth::user()->isStudent() && session()->has('answered_challenges')) {
            $answeredChallenges = session('answered_challenges');
        }
        
        return view('challenges.index', compact('challenges', 'teachers', 'teacherId', 'answeredChallenges'));
    }

    /**
     * Show the form for creating a new challenge.
     */
    public function create()
    {
        if (!Auth::user()->isTeacher()) {
            return redirect()->route('challenges.index')->with('error', 'Only teachers can create challenges');
        }
        
        return view('challenges.create');
    }

    /**
     * Store a newly created challenge.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isTeacher()) {
            return redirect()->route('challenges.index')->with('error', 'Only teachers can create challenges');
        }
        
        $validated = $request->validate([
            'hint' => 'required|string',
            'result' => 'required|string',
            'challenge_file' => 'required|file|mimes:txt',
        ]);
        
        $path = $request->file('challenge_file')->store('challenges', 'public');
        
        Challenge::create([
            'teacher_id' => Auth::id(),
            'hint' => $validated['hint'],
            'result' => $validated['result'],
            'file_path' => 'storage/' . $path,
        ]);
        
        return redirect()->route('challenges.index')->with('message', 'Challenge created successfully!');
    }

    /**
     * Verify a student's answer to a challenge.
     */
    public function checkAnswer(Request $request, Challenge $challenge)
    {
        if (!Auth::user()->isStudent()) {
            return redirect()->route('challenges.index');
        }
        
        $answer = trim($request->input('answer'));
        
        if ($challenge->checkAnswer($answer)) {
            // Store in session that this challenge has been answered correctly
            if (!session()->has('answered_challenges')) {
                session(['answered_challenges' => []]);
            }
            
            $answeredChallenges = session('answered_challenges');
            if (!in_array($challenge->id, $answeredChallenges)) {
                $answeredChallenges[] = $challenge->id;
                session(['answered_challenges' => $answeredChallenges]);
            }
            
            return back()->with('message', 'Congratulations! Your answer is correct.');
        }
        
        return back()->with('error', 'Incorrect answer. Please try again.');
    }

    /**
     * Serve the challenge file.
     */
    public function download(Challenge $challenge)
    {
        // For students, check if they've solved the challenge
        if (Auth::user()->isStudent()) {
            $answeredChallenges = session('answered_challenges', []);
            if (!in_array($challenge->id, $answeredChallenges)) {
                return back()->with('error', 'You must solve the challenge first');
            }
        }
        
        // For teachers, allow direct access
        if (!Auth::user()->isTeacher() && !in_array($challenge->id, session('answered_challenges', []))) {
            return back()->with('error', 'Access denied');
        }
        
        // Check if the file exists
        if (!file_exists(public_path($challenge->file_path))) {
            return back()->with('error', 'File not found');
        }
        
        // Get the filename from the path
        $filename = basename($challenge->file_path);
        
        return response()->download(public_path($challenge->file_path), $filename);
    }

    /**
     * Get the content of a challenge for display
     */
    public function getContent(Challenge $challenge)
    {
        // For students, check if they've solved the challenge
        if (Auth::user()->isStudent()) {
            $answeredChallenges = session('answered_challenges', []);
            if (!in_array($challenge->id, $answeredChallenges)) {
                return response()->json(['error' => 'Access denied'], 403);
            }
        }
        
        $content = file_get_contents(public_path($challenge->file_path));
        
        return response()->json([
            'content' => $content,
        ]);
    }
}
