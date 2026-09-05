@extends('layouts.public')
@section('title','Gallery | NACO')
@section('content')
<section class="page-hero"><div class="container"><div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / Gallery</div><h1>NACO in action.</h1><p>A growing visual archive of training, teamwork, skills development and community service.</p></div></section>
<section class="section"><div class="container"><div class="section-head"><span class="section-kicker">Gallery</span><h2>Moments that tell our story.</h2></div><div class="gallery-grid"><article><div class="gallery-tile">Skills Development</div></article><article><div class="gallery-tile">Community Service</div></article><article><div class="gallery-tile">Emergency Response</div></article><article><div class="gallery-tile">Teamwork</div></article><article><div class="gallery-tile">Youth Development</div></article><article><div class="gallery-tile">Leadership</div></article></div></div></section>
@endsection