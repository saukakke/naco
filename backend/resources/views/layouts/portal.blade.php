<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'NACO Portal' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-shell">
    <aside class="portal-sidebar">
        <a class="brand" href="{{ route('portal.dashboard') }}">NACO Portal</a>
        <nav aria-label="Portal navigation">
            <a href="{{ route('portal.dashboard') }}">Dashboard</a>
            <a href="#">Cadets</a><a href="#">Units</a><a href="#">Courses</a>
            <a href="#">Instructors</a><a href="#">Warrants</a><a href="#">Ranks</a>
            <a href="#">Posts</a><a href="#">Promotions</a><a href="#">Demotions</a>
        </nav>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Sign out</button></form>
    </aside>
    <main class="portal-content">@yield('content')</main>
</body>
</html>
