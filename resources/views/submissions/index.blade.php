@extends('layouts.app', ['title' => 'View Submissions'])

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Student Submissions</h2>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Filter Submissions</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('submissions.index') }}" method="get" class="row g-3">
                    <div class="col-md-5">
                        <label for="assignment_id" class="form-label">Assignment</label>
                        <select class="form-select" id="assignment_id" name="assignment_id">
                            <option value="0">All Assignments</option>
                            @foreach ($assignments as $assignment)
                                <option value="{{ $assignment->id }}" {{ $assignmentId == $assignment->id ? 'selected' : '' }}>
                                    {{ $assignment->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-5">
                        <label for="student_id" class="form-label">Student</label>
                        <select class="form-select" id="student_id" name="student_id">
                            <option value="0">All Students</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}" {{ $studentId == $student->id ? 'selected' : '' }}>
                                    {{ $student->fullname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Submissions</h4>
            </div>
            <div class="card-body">
                @if (count($submissions) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Assignment</th>
                                    <th>File</th>
                                    <th>Submitted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($submissions as $submission)
                                <tr>
                                    <td>{{ $submission->student->fullname }}</td>
                                    <td>{{ $submission->assignment->title }}</td>
                                    <td>{{ $submission->filename }}</td>
                                    <td>{{ $submission->created_at->format('M j, Y g:i A') }}</td>
                                    <td class="d-flex">
                                        <a href="{{ $submission->getDownloadUrl() }}" class="btn btn-sm btn-info me-2">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        <a href="{{ route('profile.show', $submission->student) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-user"></i> Student Profile
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> No submissions found with the selected filters.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit the form when selections change
        const assignmentSelect = document.getElementById('assignment_id');
        const studentSelect = document.getElementById('student_id');
        
        assignmentSelect.addEventListener('change', function() {
            this.form.submit();
        });
        
        studentSelect.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush
