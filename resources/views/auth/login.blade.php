@extends('layouts.app', ['title' => 'Login - Classroom Management System'])

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="text-center mb-4">
            <h1 class="display-4 text-primary"><i class="fas fa-graduation-cap me-2"></i></h1>
            <h2>Classroom Management System</h2>
            <p class="text-muted">Login to access your classroom dashboard</p>
        </div>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Login</h4>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
                    </div>
                @endif
                
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label"><i class="fas fa-user me-2"></i>Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label"><i class="fas fa-lock me-2"></i>Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block w-100">Login</button>
                </form>
            </div>
        </div>
        <div class="mt-4 text-center p-3 bg-white rounded shadow-sm">
            <h5 class="text-muted">Default Accounts</h5>
            <div class="row">
                <div class="col-md-6 border-end">
                    <p class="mb-2"><strong>Teacher:</strong></p>
                    <code>teacher1 / 123456a@A</code>
                </div>
                <div class="col-md-6">
                    <p class="mb-2"><strong>Student:</strong></p>
                    <code>student1 / 123456a@A</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
