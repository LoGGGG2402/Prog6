@extends('layouts.app', ['title' => 'Submit Assignment'])

@section('content')
<div class="row mb-3">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2>Submit Assignment</h2>
        <a href="{{ route('assignments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Assignments
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Assignment Details</h4>
            </div>
            <div class="card-body">
                <h5>{{ $assignment->title }}</h5>
                <p class="text-muted">Assigned by: {{ $assignment->teacher->fullname }}</p>
                <div class="mb-3">
                    <strong>Description:</strong>
                    <p>{{ $assignment->description ?: 'No description provided.' }}</p>
                </div>
                <div class="mb-3">
                    <strong>Assignment File:</strong>
                    <p>
                        <a href="{{ route('assignments.download', $assignment) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-download"></i> {{ $assignment->filename }}
                        </a>
                    </p>
                </div>
                <div class="mb-3">
                    <strong>Created At:</strong>
                    <p>{{ $assignment->created_at->format('M j, Y g:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>
                    @if ($submission)
                        Update Submission
                    @else
                        Submit Assignment
                    @endif
                </h4>
            </div>
            <div class="card-body">
                @if ($submission)
                    <div class="alert alert-info mb-3">
                        <strong>Current Submission:</strong> {{ $submission->filename }}<br>
                        <strong>Submitted:</strong> {{ $submission->created_at->format('M j, Y g:i A') }}
                    </div>
                @endif
                
                <form action="{{ route('submissions.store', $assignment) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="submission_file" class="form-label">
                            {{ $submission ? 'Update Your Submission File' : 'Choose Your Submission File' }}
                        </label>
                        <input type="file" class="form-control @error('submission_file') is-invalid @enderror" id="submission_file" name="submission_file" required>
                        <div class="form-text">Supported formats: PDF, DOC, DOCX, TXT, ZIP</div>
                        @error('submission_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-upload me-1"></i>
                        {{ $submission ? 'Update Submission' : 'Submit Assignment' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
