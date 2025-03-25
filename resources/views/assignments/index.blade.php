@extends('layouts.app', ['title' => 'Assignments'])

@section('content')
<div class="row mb-3">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2>Assignments</h2>
        <div class="d-flex">
            <!-- Teacher Filter Dropdown -->
            <div class="dropdown me-2">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="teacherFilterDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ $teacherId > 0 ? 'Filter: ' . $teachers->where('id', $teacherId)->first()->fullname : 'All Teachers' }}
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="teacherFilterDropdown">
                    <a class="dropdown-item {{ $teacherId == 0 ? 'active' : '' }}" href="{{ route('assignments.index') }}">All Teachers</a>
                    <div class="dropdown-divider"></div>
                    @foreach ($teachers as $teacher)
                        <a class="dropdown-item {{ $teacherId == $teacher->id ? 'active' : '' }}" 
                            href="{{ route('assignments.index', ['teacher_id' => $teacher->id]) }}">
                            {{ $teacher->fullname }}
                        </a>
                    @endforeach
                </div>
            </div>
            
            @if (Auth::user()->isTeacher())
            <a href="{{ route('assignments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Assignment
            </a>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                @if (count($assignments) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Teacher</th>
                                    <th>File</th>
                                    <th>Created At</th>
                                    @if (Auth::user()->isStudent())
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assignments as $assignment)
                                <tr>
                                    <td>{{ $assignment->title }}</td>
                                    <td>{{ $assignment->description }}</td>
                                    <td>{{ $assignment->teacher->fullname }}</td>
                                    <td>
                                        <a href="{{ $assignment->getDownloadUrl() }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-download"></i> {{ $assignment->filename }}
                                        </a>
                                    </td>
                                    <td>{{ $assignment->created_at->format('d M Y') }}</td>
                                    @if (Auth::user()->isStudent())
                                    <td>
                                        <a href="{{ route('submissions.create', $assignment) }}" class="btn btn-sm {{ $assignment->has_submitted ? 'btn-warning' : 'btn-success' }}">
                                            @if ($assignment->has_submitted)
                                                <i class="fas fa-edit"></i> Update Submission
                                            @else
                                                <i class="fas fa-upload"></i> Submit
                                            @endif
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center">No assignments available yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
