<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\User;
use App\Rules\SecureFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

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
            'result' => 'required|string|max:255',
            'challenge_file' => [
                'required',
                'file',
                'max:1024', // 1MB max
                new SecureFile(['text']),
            ],
        ]);
        
        $path = $request->file('challenge_file')->store(
            Config::get('filesystems.uploads.challenges'),
            'private'
        );
        
        Challenge::create([
            'teacher_id' => Auth::id(),
            'hint' => $validated['hint'],
            'result' => $validated['result'],
            'file_path' => $path,
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
}
