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
                            <div class="message {{ $message->sender_id == Auth::id() ? 'message-sender' : 'message-receiver' }}" id="message-{{ $message->id }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>
                                        @if ($message->sender_id == Auth::id())
                                            You
                                        @else
                                            {{ $message->sender->fullname }}
                                        @endif
                                    </strong>
                                    
                                    <div class="message-actions">
                                        @if ($message->sender_id == Auth::id())
                                            <button type="button" class="btn btn-sm btn-link text-secondary edit-message-btn" 
                                                    data-message-id="{{ $message->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <form action="{{ route('messages.destroy', $message) }}" method="post" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('Are you sure you want to delete this message?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('profile.show', $message->sender_id) }}" class="btn btn-sm btn-link text-primary" 
                                               title="Visit {{ $message->sender->fullname }}'s Profile">
                                                <i class="fas fa-reply"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="message-content mt-2" id="content-{{ $message->id }}">
                                    {{ $message->message }}
                                </div>
                                
                                <!-- Inline edit form (hidden by default) -->
                                <div class="message-edit-form mt-2" id="edit-form-{{ $message->id }}" style="display: none;">
                                    <form action="{{ route('messages.update', $message) }}" method="post" class="inline-edit-form">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-2">
                                            <textarea class="form-control" name="message" rows="3" required>{{ $message->message }}</textarea>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-sm btn-secondary me-2 cancel-edit" data-message-id="{{ $message->id }}">Cancel</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                                
                                <div class="message-meta mt-1">
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
                    <form action="{{ route('messages.store', $user) }}" method="post" id="messageForm">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all edit message buttons
        const editButtons = document.querySelectorAll('.edit-message-btn');
        const cancelButtons = document.querySelectorAll('.cancel-edit');
        
        // Add click event to all edit buttons
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const messageId = this.getAttribute('data-message-id');
                
                // Hide content and show edit form
                document.getElementById(`content-${messageId}`).style.display = 'none';
                document.getElementById(`edit-form-${messageId}`).style.display = 'block';
            });
        });
        
        // Add click event to all cancel buttons
        cancelButtons.forEach(button => {
            button.addEventListener('click', function() {
                const messageId = this.getAttribute('data-message-id');
                
                // Show content and hide edit form
                document.getElementById(`content-${messageId}`).style.display = 'block';
                document.getElementById(`edit-form-${messageId}`).style.display = 'none';
            });
        });
    });
</script>
@endpush
