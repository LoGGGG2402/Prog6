@extends('layouts.app', ['title' => 'Create Assignment'])

@section('content')
<div class="row mb-3">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2>Create Assignment</h2>
        <a href="{{ route('assignments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Assignments
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Assignment Details</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('assignments.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="assignment_file" class="form-label">Assignment File</label>
                        <input type="file" class="form-control @error('assignment_file') is-invalid @enderror" id="assignment_file" name="assignment_file" required>
                        <div class="form-text">Upload a file (PDF, DOC, DOCX, TXT, ZIP)</div>
                        @error('assignment_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Assignment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
