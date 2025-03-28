<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\User;
use App\Rules\SecureFile;
use App\Helpers\FileValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments.
     */
    public function index(Request $request)
    {
        $teacherId = $request->query('teacher_id', 0);
        $teachers = User::teachers()->get();
        
        if (Auth::user()->isStudent()) {
            if ($teacherId > 0) {
                $assignments = Assignment::with('teacher')
                    ->where('teacher_id', $teacherId)
                    ->get()
                    ->map(function ($assignment) {
                        $assignment->has_submitted = $assignment->hasSubmitted(Auth::id());
                        return $assignment;
                    });
            } else {
                $assignments = Assignment::with('teacher')
                    ->get()
                    ->map(function ($assignment) {
                        $assignment->has_submitted = $assignment->hasSubmitted(Auth::id());
                        return $assignment;
                    });
            }
        } else {
            // For teachers
            if ($teacherId > 0) {
                $assignments = Assignment::with('teacher')
                    ->where('teacher_id', $teacherId)
                    ->get();
            } else {
                $assignments = Assignment::with('teacher')->get();
            }
        }
        
        return view('assignments.index', compact('assignments', 'teachers', 'teacherId'));
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create()
    {
        if (!Auth::user()->isTeacher()) {
            return redirect()->route('assignments.index')->with('error', 'Only teachers can create assignments');
        }
        
        return view('assignments.create');
    }

    /**
     * Store a newly created assignment.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isTeacher()) {
            return redirect()->route('assignments.index')->with('error', 'Only teachers can create assignments');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assignment_file' => [
                'required',
                'file',
                'max:10240', // 10MB max
                new SecureFile(['document', 'archive']),
            ],
        ]);
        
        // Sanitize inputs to prevent XSS
        $title = strip_tags($validated['title']);
        $description = $validated['description'] ? strip_tags($validated['description']) : null;
        
        // Get original filename and sanitize it
        $originalName = $request->file('assignment_file')->getClientOriginalName();
        $fileName = FileValidator::sanitizeFilename($originalName);
        
        $path = $request->file('assignment_file')->store(
            Config::get('filesystems.uploads.assignments'),
            'private'
        );
        
        // Validate stored path to prevent directory traversal
        if (!$path || strpos($path, '..') !== false) {
            return redirect()->back()->with('error', 'Invalid file path detected.');
        }
        
        Assignment::create([
            'teacher_id' => Auth::id(),
            'title' => $title,
            'description' => $description,
            'file_path' => $path,
            'filename' => $fileName,
        ]);
        
        return redirect()->route('assignments.index')->with('message', 'Assignment created successfully!');
    }

    /**
     * Download has been removed - now handled by FileController
     */
}
