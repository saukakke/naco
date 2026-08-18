@extends('layouts.portal')
@section('content')
<div class="page-header"><div><p class="eyebrow">Warrant verification</p><h1>{{ $warrant->warrant_number }}</h1><p>Issued to {{ $warrant->cadet->first_name }} {{ $warrant->cadet->last_name }} after successful qualification.</p></div></div>
<div class="form-card"><dl><dt>Course</dt><dd>{{ $warrant->course->name }}</dd><dt>Issued</dt><dd>{{ $warrant->issued_at?->format('d M Y') }}</dd><dt>Expires</dt><dd>{{ $warrant->expires_at?->format('d M Y') }}</dd><dt>Status</dt><dd>{{ $warrant->is_valid ? 'ACTIVE — VALID INSTRUCTOR' : 'EXPIRED / INACTIVE' }}</dd></dl></div>
@endsection
