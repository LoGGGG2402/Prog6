<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Send a message to another user.
     */
    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);
        
        // Check if user is trying to message themselves
        if (Auth::id() == $user->id) {
            return back()->with('error', 'You cannot send messages to yourself.');
        }
        
        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'message' => $validated['message'],
            'is_read' => false,
        ]);
        
        return back()->with('message', 'Message sent successfully!');
    }

    /**
     * Update an existing message.
     */
    public function update(Request $request, Message $message)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);
        
        // Check if the user is the sender of the message
        if (Auth::id() != $message->sender_id) {
            return back()->with('error', "You don't have permission to edit this message");
        }
        
        $message->update([
            'message' => $validated['message'],
        ]);
        
        return back()->with('message', 'Message updated successfully!');
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message)
    {
        // Check if the user is the sender of the message
        if (Auth::id() != $message->sender_id) {
            return back()->with('error', "You don't have permission to delete this message");
        }
        
        $message->delete();
        
        return back()->with('message', 'Message deleted successfully!');
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead(User $user)
    {
        Message::markConversationAsRead(Auth::id(), $user->id);
        
        return back();
    }
}
