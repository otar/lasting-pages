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