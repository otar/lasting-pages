@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <div class="container mt-4 pb-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @include('partials.add-page-form')
        
        @include('partials.pages-list')
    </div>
@endsection