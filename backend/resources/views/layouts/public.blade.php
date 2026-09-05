<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="@yield('meta_description','NACO — discipline, skills and self-reliance.')">
<title>@yield('title','NACO | Normal Apprenticeship Company')</title>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<div class="topbar"><div class="container topbar-inner"><div class="topbar-links"><a href="tel:+2348130144920">+234 813 014 4920</a><a href="mailto:hello@naco.org.ng">hello@naco.org.ng</a><span>Kaduna, Nigeria</span></div><div class="socials"><span>Skills. Discipline. Self-Reliance.</span></div></div></div>
<header class="site-header"><div class="container nav-inner">
<a class="brand" href="{{ route('home') }}"><span class="brand-mark">N</span><span><strong>NACO</strong><small>Normal Apprenticeship Company</small></span></a>
<button class="menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" data-menu-toggle="#primary-nav">☰</button>
<nav id="primary-nav" class="nav-links" aria-label="Primary navigation">
<a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
<a class="{{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
<a class="{{ request()->routeIs('programs') ? 'active' : '' }}" href="{{ route('programs') }}">Programs</a>
<a class="{{ request()->routeIs('leadership') ? 'active' : '' }}" href="{{ route('leadership') }}">Leadership</a>
<a class="{{ request()->routeIs('teams') ? 'active' : '' }}" href="{{ route('teams') }}">Team</a>
<a class="{{ request()->routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">Gallery</a>
<a class="{{ request()->routeIs('blog') ? 'active' : '' }}" href="{{ route('blog') }}">Blog</a>
<a class="nav-cta" href="{{ route('portal.login') }}">Portal Login ↗</a>
</nav></div></header>
<main id="main">@yield('content')</main>
<footer class="site-footer"><div class="container footer-grid"><div><a class="brand brand-light" href="{{ route('home') }}"><span class="brand-mark">N</span><span>NACO</span></a><p>Normal Apprenticeship Company develops practical skills, discipline, emergency readiness and self-reliance for stronger communities.</p></div><div><h4>Explore</h4><ul><li><a href="{{ route('about') }}">About NACO</a></li><li><a href="{{ route('programs') }}">Programs</a></li><li><a href="{{ route('impact') }}">Impact</a></li></ul></div><div><h4>Discover</h4><ul><li><a href="{{ route('teams') }}">Team</a></li><li><a href="{{ route('gallery') }}">Gallery</a></li><li><a href="{{ route('verify') }}">Verify Personnel</a></li></ul></div><div><h4>Contact</h4><ul><li><a href="tel:+2348130144920">+234 813 014 4920</a></li><li><a href="mailto:hello@naco.org.ng">hello@naco.org.ng</a></li><li>Kaduna, Nigeria</li></ul></div></div><div class="container footer-bottom"><span>© {{ date('Y') }} Normal Apprenticeship Company (NACO).</span><span>Building a self-reliant generation.</span></div></footer>
<script src="{{ asset('js/app.js') }}"></script>
</body></html>