<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">My Saved Pages ({{ $pages->count() }})</h4>
            
            @if($pages->count() > 0)
                <div class="text-muted small">
                    Sort: 
                    @if($sortOrder === 'desc')
                        <strong>Newest First</strong>
                    @else
                        <a href="{{ route('dashboard', ['sort' => 'desc']) }}" class="text-decoration-none">Newest First</a>
                    @endif
                    |
                    @if($sortOrder === 'asc')
                        <strong>Oldest First</strong>
                    @else
                        <a href="{{ route('dashboard', ['sort' => 'asc']) }}" class="text-decoration-none">Oldest First</a>
                    @endif
                </div>
            @endif
        </div>

        @if($pages->count() > 0)
            <div class="row">
                @foreach($pages as $page)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <a href="#" class="card-main-link">
                            <div class="card h-100 position-relative @if($page->is_pending) is-pending @endif">
                                @if($page->is_pending)
                                    <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-warning text-bg-warning">
                                        <div class="spinner-border spinner-border-sm" role="status" style="width: 0.75rem; height: 0.75rem;"></div> Fetching...
                                    </span>
                                @endif
                                <div class="card-body d-flex flex-column">
                                    @if($page->title)
                                        <h6 class="card-title">
                                            {{ Str::limit($page->title, 40) }}
                                        </h6>
                                    @endif
                                    <p class="card-text small">
                                        <span class="text-muted">{{ Str::limit($page->url, 40) }}</span>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <abbr title="{{ $page->created_at->format('Y-m-d H:i') }}">{{ $page->created_at->diffForHumans() }}</abbr>
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </a>
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
