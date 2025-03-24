@extends('layouts.app', ['title' => 'Create Challenge'])

@section('content')
<div class="row mb-3">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2>Create Challenge</h2>
        <a href="{{ route('challenges.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Challenges
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Challenge Details</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('challenges.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="hint" class="form-label">Hint</label>
                        <textarea class="form-control @error('hint') is-invalid @enderror" id="hint" name="hint" rows="4" required>{{ old('hint') }}</textarea>
                        <div class="form-text">Provide a hint that guides students to solve the challenge</div>
                        @error('hint')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="result" class="form-label">Expected Result (Answer)</label>
                        <input type="text" class="form-control @error('result') is-invalid @enderror" id="result" name="result" value="{{ old('result') }}" required>
                        <div class="form-text">The exact answer that students need to provide to solve the challenge</div>
                        @error('result')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="challenge_file" class="form-label">Challenge Content File</label>
                        <input type="file" class="form-control @error('challenge_file') is-invalid @enderror" id="challenge_file" name="challenge_file" required>
                        <div class="form-text">Upload a text file (.txt) containing the challenge content</div>
                        @error('challenge_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Challenge
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
