@extends('layouts.app')

@section('title', 'Welcome')

@section('body-class', 'bg-light')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body text-center">
                    <h1 class="card-title display-4 mb-4">Welcome to Lasting Pages</h1>
                    <p class="card-text lead mb-4">A simple web application for creating content that stands the test of time.</p>
                    
                    @guest
                        <div class="d-flex gap-3 justify-content-center">
                            <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary">Register</a>
                        </div>
                    @else
                        <div>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@endsection