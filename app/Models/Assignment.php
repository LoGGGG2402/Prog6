<?php

namespace App\Models;

use App\Http\Controllers\FileController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Assignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'file_path',
        'filename',
    ];

    /**
     * Get the teacher that owns the assignment
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the submissions for this assignment
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Check if a student has submitted this assignment
     */
    public function hasSubmitted($studentId)
    {
        return $this->submissions()->where('student_id', $studentId)->exists();
    }

    /**
     * Get submission for a specific student
     */
    public function getStudentSubmission($studentId)
    {
        return $this->submissions()->where('student_id', $studentId)->first();
    }

    /**
     * Get secure download URL for this assignment
     */
    public function getDownloadUrl()
    {
        $fileController = App::make(FileController::class);
        return $fileController->generateDownloadUrl($this->id, 'assignment');
    }
}
