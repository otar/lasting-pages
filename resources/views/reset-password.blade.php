@extends('layouts.main')

@section('title', 'Reset Password')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Reset Password</h2>
                    @if($isExpired ?? false)
                        <div class="alert alert-warning" role="alert">
                            <strong>This password reset link has expired.</strong><br>
                            Password reset links are valid for 1 hour for security reasons.
                            @if($recentEmail ?? false)
                                <a href="{{ route('password.request') }}?email={{ urlencode($recentEmail) }}" class="alert-link">Request a new password reset link</a> with your email pre-filled.
                            @else
                                Please <a href="{{ route('password.request') }}" class="alert-link">request a new password reset link</a>.
                            @endif
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}" @if($isExpired ?? false) style="display: none;" @endif>
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ $email ?? old('email') }}" 
                                   placeholder="Email Address"
                                   required 
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="New Password"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Confirm New Password"
                                   required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                Reset Password
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection