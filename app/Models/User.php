<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // Add name to fillable attributes
        'username',
        'fullname',
        'email',
        'phone',
        'password',
        'role',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Check if user is a teacher
     *
     * @return bool
     */
    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    /**
     * Check if user is a student
     *
     * @return bool
     */
    public function isStudent()
    {
        return $this->role === 'student';
    }

    /**
     * Get the assignments created by this teacher
     */
    public function teacherAssignments()
    {
        return $this->hasMany(Assignment::class, 'teacher_id');
    }

    /**
     * Get the submissions made by this student
     */
    public function studentSubmissions()
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    /**
     * Get the challenges created by this teacher
     */
    public function challenges()
    {
        return $this->hasMany(Challenge::class, 'teacher_id');
    }

    /**
     * Get messages sent by this user
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get messages received by this user
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get unread messages for this user
     */
    public function unreadMessages()
    {
        return $this->receivedMessages()->where('is_read', false);
    }

    /**
     * Scope a query to only include student users
     */
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    /**
     * Scope a query to only include teacher users
     */
    public function scopeTeachers($query)
    {
        return $query->where('role', 'teacher');
    }
}
