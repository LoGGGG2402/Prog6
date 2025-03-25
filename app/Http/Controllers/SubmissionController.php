<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Rules\SecureFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class SubmissionController extends Controller
{
    /**
     * Display a listing of submissions (teacher view).
     */
    public function index(Request $request)
    {
        if (!Auth::user()->isTeacher()) {
            return redirect()->route('submissions.my');
        }
        
        $assignmentId = $request->query('assignment_id', 0);
        $studentId = $request->query('student_id', 0);
        
        $assignments = Assignment::all();
        $students = User::students()->get();
        
        $query = Submission::with(['student', 'assignment']);
        
        if ($assignmentId > 0) {
            $query->where('assignment_id', $assignmentId);
        }
        
        if ($studentId > 0) {
            $query->where('student_id', $studentId);
        }
        
        $submissions = $query->get();
        
        return view('submissions.index', compact(
            'submissions', 
            'assignments', 
            'students', 
            'assignmentId', 
            'studentId'
        ));
    }

    /**
     * Display the student's submissions.
     */
    public function mySubmissions()
    {
        if (!Auth::user()->isStudent()) {
            return redirect()->route('submissions.index');
        }
        
        $submissions = Submission::with('assignment')
            ->where('student_id', Auth::id())
            ->get();
        
        return view('submissions.my', compact('submissions'));
    }

    /**
     * Show the form for submitting an assignment.
     */
    public function create(Assignment $assignment)
    {
        if (!Auth::user()->isStudent()) {
            return redirect()->route('assignments.index');
        }
        
        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', Auth::id())
            ->first();
        
        return view('submissions.create', compact('assignment', 'submission'));
    }

    /**
     * Store a newly created submission in storage.
     */
    public function store(Request $request, Assignment $assignment)
    {
        if (!Auth::user()->isStudent()) {
            return redirect()->route('assignments.index')->with('error', 'Only students can submit assignments');
        }
        
        $validated = $request->validate([
            'submission_file' => [
                'required',
                'file',
                'max:10240', // 10MB max
                new SecureFile(['document', 'archive']),
            ],
        ]);
        
        $fileName = $request->file('submission_file')->getClientOriginalName();
        
        // Check if the student has already submitted this assignment
        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', Auth::id())
            ->first();
        
        if ($submission) {
            // Update existing submission
            // Delete old file if it exists
            if (Storage::disk('private')->exists($submission->file_path)) {
                Storage::disk('private')->delete($submission->file_path);
            }
            
            $path = $request->file('submission_file')->store(
                Config::get('filesystems.uploads.submissions'),
                'private'
            );
            
            $submission->update([
                'file_path' => $path,
                'filename' => $fileName,
            ]);
            
            $message = 'Submission updated successfully!';
        } else {
            // Create new submission
            $path = $request->file('submission_file')->store(
                Config::get('filesystems.uploads.submissions'),
                'private'
            );
            
            Submission::create([
                'assignment_id' => $assignment->id,
                'student_id' => Auth::id(),
                'file_path' => $path,
                'filename' => $fileName,
            ]);
            
            $message = 'Assignment submitted successfully!';
        }
        
        return redirect()->route('assignments.index')->with('message', $message);
    }

    /**
     * Download has been removed - now handled by FileController
     */
}
