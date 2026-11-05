<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Beheer De Hamershof</title>
    @vite('resources/css/admin.css')
</head>
<body class="admin">
    <header class="admin-header">
        <a class="admin-logo" href="{{ route('admin.stores.index') }}">
            de <em>Hamershof</em>
            <small>beheer</small>
        </a>

        <nav class="admin-nav" aria-label="Beheernavigatie">
            <a href="{{ route('admin.stores.index') }}" @class(['is-active' => request()->routeIs('admin.stores.*')])>Winkels</a>
            <a href="{{ route('home') }}" target="_blank" rel="noopener">Bekijk website <span aria-hidden="true">&#8599;</span></a>
        </nav>

        <div class="admin-user">
            <span>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="admin-link-button">Uitloggen</button>
            </form>
        </div>
    </header>

    <main class="admin-main">
        @if (session('success'))
            <p class="admin-alert admin-alert--success" role="status">{{ session('success') }}</p>
        @endif

        @yield('content')
    </main>
</body>
</html>
