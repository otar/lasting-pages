@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Add New Page</h5>
                            <p class="card-text text-muted mb-0">Save a URL to read later</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('pages.store') }}" class="mt-3">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="url" class="form-control @error('url') is-invalid @enderror" 
                                       name="url" placeholder="Enter URL (e.g., https://example.com)" 
                                       value="{{ old('url') }}" required>
                                @error('url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       name="title" placeholder="Title (optional)" 
                                       value="{{ old('title') }}" maxlength="250">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <button type="submit" class="btn btn-primary w-100">Save Page</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">My Saved Pages ({{ $pages->count() }})</h4>
            
            @if($pages->count() > 0)
                <div class="row">
                    @foreach($pages as $page)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">
                                        @if($page->title)
                                            {{ Str::limit($page->title, 50) }}
                                        @else
                                            <span class="text-muted">Untitled</span>
                                        @endif
                                    </h6>
                                    <p class="card-text small">
                                        <span class="text-muted">{{ Str::limit($page->url, 60) }}</span>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted">{{ $page->created_at->diffForHumans() }}</small>
                                    </p>
                                    <div class="mt-auto">
                                        <div class="btn-group w-100" role="group">
                                            <a href="{{ $page->url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                Visit
                                            </a>
                                            <form method="POST" action="{{ route('pages.destroy', $page) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Delete this page?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <h5 class="text-muted mb-3">No pages saved yet</h5>
                    <p class="text-muted">Use the form above to save your first page.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection