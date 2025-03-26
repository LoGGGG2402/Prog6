@extends('layouts.app', ['title' => 'Manage Students'])

@section('content')
<div class="row mb-3">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2>Manage Users</h2>
        <div>
            <a href="{{ route('users.create-teacher') }}" class="btn btn-primary me-2">
                <i class="fas fa-user-plus"></i> Add Teacher
            </a>
            <a href="{{ route('users.create-student') }}" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Add Student
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Student Directory</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Avatar</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                            <tr>
                                <td>
                                    @if (!empty($student->avatar))
                                        <img src="{{ $student->avatar }}" alt="Avatar" class="avatar-sm">
                                    @else
                                        <div class="avatar-placeholder">
                                            {{ substr($student->fullname, 0, 1) }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $student->fullname }}</td>
                                <td>{{ $student->username }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $student->phone }}</td>
                                <td>
                                    <a href="{{ route('profile.show', $student) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-user"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
