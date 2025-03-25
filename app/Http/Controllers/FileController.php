<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Challenge;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Generate a secure file token for download
     */
    public function generateSecureToken($fileId, $type)
    {
        $token = Str::random(64);
        session(["file_token_{$type}_{$fileId}" => [
            'token' => $token,
            'expires' => now()->addMinutes(5)->timestamp
        ]]);
        return $token;
    }
    
    /**
     * Validate a secure file token
     */
    private function validateToken($token, $fileId, $type)
    {
        $sessionKey = "file_token_{$type}_{$fileId}";
        if (!session()->has($sessionKey)) {
            return false;
        }
        
        $tokenData = session($sessionKey);
        if ($tokenData['token'] !== $token || $tokenData['expires'] < now()->timestamp) {
            // Token expired or invalid, remove it
            session()->forget($sessionKey);
            return false;
        }
        
        // Token is valid, remove it (one-time use)
        session()->forget($sessionKey);
        return true;
    }

    /**
     * Generate a download URL with secure token
     */
    public function generateDownloadUrl($id, $type)
    {
        $token = $this->generateSecureToken($id, $type);
        return route('file.download', ['type' => $type, 'id' => $id, 'token' => $token]);
    }

    /**
     * Handle secure file downloads
     */
    public function download(Request $request, $type, $id)
    {
        // Validate the token
        if (!$this->validateToken($request->token, $id, $type)) {
            abort(403, 'Invalid or expired token');
        }

        switch ($type) {
            case 'assignment':
                return $this->downloadAssignment($id);
            case 'submission':
                return $this->downloadSubmission($id);
            case 'challenge':
                return $this->downloadChallenge($id);
            default:
                abort(404, 'Unknown file type');
        }
    }

    /**
     * Download assignment file with authorization check
     */
    private function downloadAssignment($id)
    {
        $assignment = Assignment::findOrFail($id);
        
        if (!Storage::disk('private')->exists($assignment->file_path)) {
            abort(404, 'File not found');
        }
        
        return Storage::disk('private')->download(
            $assignment->file_path, 
            $assignment->filename
        );
    }

    /**
     * Download submission file with authorization check
     */
    private function downloadSubmission($id)
    {
        $submission = Submission::findOrFail($id);
        
        // Check permissions - only owner student or teachers can download
        if (Auth::id() !== $submission->student_id && !Auth::user()->isTeacher()) {
            abort(403, 'Unauthorized access');
        }
        
        if (!Storage::disk('private')->exists($submission->file_path)) {
            abort(404, 'File not found');
        }
        
        return Storage::disk('private')->download(
            $submission->file_path, 
            $submission->filename
        );
    }

    /**
     * Download challenge file with authorization check
     */
    private function downloadChallenge($id)
    {
        $challenge = Challenge::findOrFail($id);
        
        // For students, check if they've solved the challenge
        if (Auth::user()->isStudent()) {
            $answeredChallenges = session('answered_challenges', []);
            if (!in_array($challenge->id, $answeredChallenges)) {
                abort(403, 'You must solve the challenge first');
            }
        }
        
        if (!Storage::disk('private')->exists($challenge->file_path)) {
            abort(404, 'File not found');
        }
        
        return Storage::disk('private')->download(
            $challenge->file_path, 
            basename($challenge->file_path)
        );
    }
}
