@extends('layouts.app', ['title' => 'Home - Classroom Management System'])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>User Directory</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Avatar</th>
                                <th>Full Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>
                                    @if (!empty($user->avatar))
                                        <img src="{{ $user->avatar }}" alt="Avatar" class="avatar-sm">
                                    @else
                                        <img src="{{ asset('img/default-avatar.png') }}" alt="Default Avatar" class="avatar-sm">
                                    @endif
                                </td>
                                <td>{{ $user->fullname }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <a href="{{ route('profile.show', $user->id) }}" class="btn btn-sm btn-info">View Profile</a>
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

@if (Auth::user()->isTeacher())
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Teacher Dashboard</h4>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="{{ route('students.index') }}" class="list-group-item list-group-item-action">Manage Students</a>
                    <a href="{{ route('assignments.index') }}" class="list-group-item list-group-item-action">Manage Assignments</a>
                    <a href="{{ route('submissions.index') }}" class="list-group-item list-group-item-action">View Submissions</a>
                    <a href="{{ route('challenges.index') }}" class="list-group-item list-group-item-action">Create Challenges</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if (Auth::user()->isStudent())
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Student Dashboard</h4>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="{{ route('assignments.index') }}" class="list-group-item list-group-item-action">View Assignments</a>
                    <a href="{{ route('submissions.my') }}" class="list-group-item list-group-item-action">My Submissions</a>
                    <a href="{{ route('challenges.index') }}" class="list-group-item list-group-item-action">Solve Challenges</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
