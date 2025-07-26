@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Welcome to your Dashboard</h5>
                    <p class="card-text">You are successfully logged in!</p>
                    <p class="text-muted">Email: {{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection