<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">Add New Page</h5>
                        {{-- <p class="card-text text-muted mb-0">Save a URL to read later</p> --}}
                    </div>
                </div>
                <form method="POST" action="{{ route('pages.store') }}" class="mt-3">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="url"
                               class="form-control
                               @error('url') is-invalid @enderror"
                               name="url"
                               placeholder="Enter URL (https://example.com)"
                               value="{{ old('url') }}"
                               required
                               aria-describedby="save-button"
                        >
                        <button type="submit" class="btn btn-primary" id="save-button">Save Page</button>
                        @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>