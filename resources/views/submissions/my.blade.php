@extends('layouts.app', ['title' => 'My Submissions'])

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <h2>My Submissions</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-file-upload me-2"></i>Assignment Submissions</h4>
                <a href="{{ route('assignments.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-tasks me-1"></i> View Assignments
                </a>
            </div>
            <div class="card-body">
                @if (count($submissions) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Assignment</th>
                                    <th>File</th>
                                    <th>Submitted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($submissions as $submission)
                                <tr>
                                    <td>{{ $submission->assignment->title }}</td>
                                    <td>{{ $submission->filename }}</td>
                                    <td>{{ $submission->created_at->format('M j, Y g:i A') }}</td>
                                    <td>
                                        <a href="{{ route('submissions.download', $submission) }}" class="btn btn-sm btn-info me-1">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        <a href="{{ route('submissions.create', $submission->assignment) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Update
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You haven't submitted any assignments yet. 
                        <a href="{{ route('assignments.index') }}" class="alert-link">View available assignments</a> to get started.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
