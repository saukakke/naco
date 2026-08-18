<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description ?? 'NACO — skills, discipline and self-reliance.' }}">
    <title>{{ $title ?? 'NACO' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <header class="site-header">
        <a class="brand" href="{{ route('home') }}">NACO</a>
        <nav aria-label="Primary navigation">
            <a href="{{ route('about') }}">About</a>
            <a href="{{ route('programs') }}">Programs</a>
            <a href="{{ route('leadership') }}">Leadership</a>
            <a href="{{ route('teams') }}">Team</a>
            <a href="{{ route('gallery') }}">Gallery</a>
            <a href="{{ route('blog') }}">Blog</a>
            <a href="{{ route('contact') }}">Contact</a>
            <a class="nav-cta" href="{{ route('login') }}">Portal Login</a>
        </nav>
    </header>
    <main id="main">@yield('content')</main>
    <footer class="site-footer"><p>© {{ date('Y') }} Normal Apprenticeship Company (NACO).</p></footer>
</body>
</html>
