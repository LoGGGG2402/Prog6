<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'submission_file' => 'required|file|mimes:pdf,doc,docx,txt,zip',
        ]);
        
        $path = $request->file('submission_file')->store('submissions', 'public');
        $fileName = $request->file('submission_file')->getClientOriginalName();
        
        // Check if the student has already submitted this assignment
        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', Auth::id())
            ->first();
        
        if ($submission) {
            // Update existing submission
            // Delete old file if it exists
            if (file_exists(public_path($submission->file_path))) {
                unlink(public_path($submission->file_path));
            }
            
            $submission->update([
                'file_path' => 'storage/' . $path,
                'filename' => $fileName,
            ]);
            
            $message = 'Submission updated successfully!';
        } else {
            // Create new submission
            Submission::create([
                'assignment_id' => $assignment->id,
                'student_id' => Auth::id(),
                'file_path' => 'storage/' . $path,
                'filename' => $fileName,
            ]);
            
            $message = 'Assignment submitted successfully!';
        }
        
        return redirect()->route('assignments.index')->with('message', $message);
    }

    /**
     * Serve the submission file.
     */
    public function download(Submission $submission)
    {
        // Security check - only allow the owner or teachers to download
        if (Auth::id() !== $submission->student_id && !Auth::user()->isTeacher()) {
            return back()->with('error', 'You do not have permission to access this file');
        }
        
        // Check if the file exists
        if (!file_exists(public_path($submission->file_path))) {
            return back()->with('error', 'File not found');
        }
        
        return response()->download(public_path($submission->file_path), $submission->filename);
    }
}
