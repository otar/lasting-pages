<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @hasSection('title')
            @yield('title') &middot; Lasting Pages
        @else
            Lasting Pages
        @endif
    </title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @if(auth()->check())
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="{{ route('dashboard') }}">Lasting Pages</a>
                <div class="navbar-nav ms-auto">
                    <div class="d-flex align-items-center me-3">
                        <img src="{{ $userAvatar }}"
                             alt="User Avatar"
                             class="rounded-circle border object-fit-cover me-2"
                             width="32"
                             height="32">
                        <span class="navbar-text">{{ $userEmail }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                    </form>
                </div>
            </div>
        </nav>
    @endif

    @yield('content')
</body>
</html>
