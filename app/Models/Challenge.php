<?php

namespace App\Models;

use App\Http\Controllers\FileController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class Challenge extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'teacher_id',
        'hint',
        'file_path',
        'result',
    ];

    /**
     * Get the teacher that created this challenge
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Check if the provided answer is correct
     */
    public function checkAnswer($answer)
    {
        return trim($answer) === trim($this->result);
    }

    /**
     * Get secure download URL for this challenge
     */
    public function getDownloadUrl()
    {
        $fileController = App::make(FileController::class);
        return $fileController->generateDownloadUrl($this->id, 'challenge');
    }
    
    /**
     * Get the content of the challenge file
     * 
     * @return string|null The content of the challenge file or null if file doesn't exist
     */
    public function getContent()
    {
        if (!$this->file_path || !Storage::disk('private')->exists($this->file_path)) {
            return null;
        }
        
        return Storage::disk('private')->get($this->file_path);
    }
}
