<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of users (homepage).
     */
    public function index()
    {
        $users = User::orderBy('fullname', 'ASC')->get();
        
        return view('home', compact('users'));
    }

    /**
     * Display the specified user profile.
     */
    public function show(User $user)
    {
        // Mark messages as read if we're viewing someone else's profile
        if ($user->id != Auth::id()) {
            Message::markConversationAsRead(Auth::id(), $user->id);
        }
        
        // Check if we're coming from a reply action
        $fromReply = request()->has('from_reply') && request()->from_reply == 1;
        $senderId = request()->input('sender_id', 0);
        
        // If we arrived at this page from clicking "Reply", mark that sender's messages as read
        if ($fromReply && $senderId > 0) {
            Message::markConversationAsRead(Auth::id(), $senderId);
        }
        
        // Fetch messages for this user
        if ($user->id == Auth::id()) {
            // If viewing own profile, get recent unread messages from all users
            $messages = Message::with('sender')
                ->where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->get();
        } else {
            // If viewing another profile, get conversation with that user
            $messages = Message::with(['sender', 'receiver'])
                ->where(function($query) use ($user) {
                    $query->where('sender_id', Auth::id())
                          ->where('receiver_id', $user->id);
                })
                ->orWhere(function($query) use ($user) {
                    $query->where('sender_id', $user->id)
                          ->where('receiver_id', Auth::id());
                })
                ->orderBy('created_at')
                ->get();
        }
        
        return view('users.profile', compact('user', 'messages'));
    }

    /**
     * Display a listing of students (teacher only).
     */
    public function students()
    {
        if (!Auth::user()->isTeacher()) {
            return redirect()->route('home');
        }
        
        $students = User::students()->get();
        
        return view('users.students', compact('students'));
    }
}
