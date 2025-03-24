@extends('layouts.app', ['title' => $user->fullname . ' - Profile'])

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Profile Information</h4>
            </div>
            <div class="card-body text-center">
                @if (!empty($user->avatar))
                    <img src="{{ $user->avatar }}" alt="Avatar" class="avatar-lg mb-3">
                @else
                    <img src="{{ asset('img/default-avatar.png') }}" alt="Default Avatar" class="avatar-lg mb-3">
                @endif
                
                <h3>{{ $user->fullname }}</h3>
                <p class="text-muted">{{ ucfirst($user->role) }}</p>
                
                <div class="list-group text-start mt-4">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-envelope me-2 text-primary"></i> Email
                        </div>
                        <div>{{ $user->email }}</div>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-phone me-2 text-primary"></i> Phone
                        </div>
                        <div>{{ $user->phone }}</div>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-user me-2 text-primary"></i> Username
                        </div>
                        <div>{{ $user->username }}</div>
                    </div>
                </div>
                
                <!-- Edit Profile Button (shown to profile owner or teachers viewing student profiles) -->
                @if (Auth::id() == $user->id || (Auth::user()->isTeacher() && $user->isStudent()))
                <button type="button" class="btn btn-primary mt-3 w-100" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fas fa-edit me-1"></i> Edit Profile
                </button>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    @if ($user->id == Auth::id())
                        Unread Messages
                    @else
                        Conversation with {{ $user->fullname }}
                    @endif
                </h4>
                
                @if ($user->id != Auth::id())
                <form action="{{ route('messages.read', $user) }}" method="post" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light" title="Mark all as read">
                        <i class="fas fa-check-double"></i> Mark all as read
                    </button>
                </form>
                @endif
            </div>
            <div class="card-body">
                @if ($messages->count() > 0)
                    <div class="message-container">
                        @foreach ($messages as $message)
                            <div class="message {{ $message->sender_id == Auth::id() ? 'message-sender' : 'message-receiver' }}">
                                <div class="d-flex justify-content-between">
                                    <strong>
                                        @if ($message->sender_id == Auth::id())
                                            You
                                        @else
                                            {{ $message->sender->fullname }}
                                        @endif
                                    </strong>
                                    
                                    @if ($message->sender_id == Auth::id())
                                    <div>
                                        <button type="button" class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#editMessageModal{{ $message->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <form action="{{ route('messages.destroy', $message) }}" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Edit Message Modal -->
                                    <div class="modal fade" id="editMessageModal{{ $message->id }}" tabindex="-1" aria-labelledby="editMessageModalLabel{{ $message->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editMessageModalLabel{{ $message->id }}">Edit Message</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('messages.update', $message) }}" method="post">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="editMessage{{ $message->id }}" class="form-label">Message</label>
                                                            <textarea class="form-control" id="editMessage{{ $message->id }}" name="message" rows="3" required>{{ $message->message }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="message-content mt-1">
                                    {{ $message->message }}
                                </div>
                                
                                <div class="message-meta">
                                    <small class="text-muted">
                                        {{ $message->created_at->format('M j, Y g:i A') }}
                                        @if ($message->receiver_id == Auth::id() && !$message->is_read)
                                            <span class="badge bg-warning text-dark">New</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        @if ($user->id == Auth::id())
                            You have no unread messages.
                        @else
                            No messages in this conversation yet.
                        @endif
                    </div>
                @endif
                
                @if ($user->id != Auth::id())
                <div class="mt-3">
                    <form action="{{ route('messages.store', $user) }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="message" class="form-label">Send a message to {{ $user->fullname }}</label>
                            <textarea class="form-control" id="message" name="message" rows="3" required placeholder="Type your message here..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Send Message
                        </button>
                    </form>
                </div>
                @elseif ($user->id == Auth::id() && $messages->count() > 0)
                <div class="mt-3 d-flex justify-content-center">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fas fa-reply me-1"></i> Return to Home
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
@if (Auth::id() == $user->id || (Auth::user()->isTeacher() && $user->isStudent()))
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.update', $user) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        @if (Auth::user()->isTeacher() && $user->isStudent())
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullname" name="fullname" value="{{ $user->fullname }}" required>
                            </div>
                        </div>
                        @endif
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="avatar" class="form-label">Avatar</label>
                                <input type="file" class="form-control" id="avatar" name="avatar">
                                <div class="form-text">Upload a new avatar image (JPG, PNG, GIF)</div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="avatar_url" class="form-label">Avatar URL</label>
                                <input type="text" class="form-control" id="avatar_url" name="avatar_url" placeholder="https://example.com/avatar.jpg">
                                <div class="form-text">Or provide a URL to an online image</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <div class="form-text">Leave blank to keep current password</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
