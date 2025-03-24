@extends('layouts.app', ['title' => 'Manage Students'])

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Manage Students</h2>
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
                                        <img src="{{ asset('img/default-avatar.png') }}" alt="Default Avatar" class="avatar-sm">
                                    @endif
                                </td>
                                <td>{{ $student->fullname }}</td>
                                <td>{{ $student->username }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $student->phone }}</td>
                                <td>
                                    <a href="{{ route('profile.show', $student->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-user"></i> View Profile
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
