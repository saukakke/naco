@extends('layouts.portal')
@section('content')
<div class="page-header"><div><p class="eyebrow">Qualification</p><h1>Instructor Courses</h1><p>Complete a qualifying course, satisfy payment requirements and pass training before a warrant can be issued.</p></div></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="cards">@forelse($courses as $course)<article class="card"><h2>{{ $course->name }}</h2><p>{{ $course->description }}</p><p><strong>Course fee:</strong> {{ number_format((float)$course->fee,2) }}</p><form method="POST" action="{{ route('portal.instructors.enroll',$course) }}">@csrf<button class="btn btn-primary" type="submit">Enroll</button></form></article>@empty<p>No instructor-qualifying courses are currently available.</p>@endforelse</div>
@endsection
