<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','NACO')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark">N</span><span>NACO</span></a>
        <nav class="nav-links" aria-label="Primary navigation">
            <a href="{{ route('about') }}">About</a>
            <a href="{{ route('programs') }}">Programs</a>
            <a href="{{ route('leadership') }}">Leadership</a>
            <a href="{{ route('teams') }}">Team</a>
            <a href="{{ route('gallery') }}">Gallery</a>
            <a href="{{ route('blog') }}">Blog</a>
            <a href="{{ route('portal.login') }}">Portal</a>
        </nav>
    </header>
    <main id="main">@yield('content')</main>
    <footer class="site-footer">
        <p>© {{ date('Y') }} NACO — Normal Apprenticeship Company</p>
    </footer>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
