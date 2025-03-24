<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
     * Get the challenge content
     */
    public function getContent()
    {
        if (Storage::exists(str_replace('storage/', 'public/', $this->file_path))) {
            return Storage::get(str_replace('storage/', 'public/', $this->file_path));
        } elseif (file_exists(public_path($this->file_path))) {
            return file_get_contents(public_path($this->file_path));
        }
        
        return null;
    }
}
