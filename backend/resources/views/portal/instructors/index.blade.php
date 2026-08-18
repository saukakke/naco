@extends('layouts.portal')
@section('content')
<div class="page-header"><div><p class="eyebrow">Training & warrants</p><h1>Instructor Status</h1><p>Instructor authority is active only while a valid warrant exists.</p></div><a class="btn btn-primary" href="{{ route('portal.instructors.courses') }}">Instructor Courses</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>Cadet</th><th>Warrant</th><th>Issued</th><th>Expires</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($cadets as $cadet) @php($warrant=$cadet->warrants->first())<tr><td>{{ $cadet->first_name }} {{ $cadet->last_name }}</td><td>{{ $warrant?->warrant_number ?? 'None' }}</td><td>{{ $warrant?->issued_at?->format('d M Y') ?? '—' }}</td><td>{{ $warrant?->expires_at?->format('d M Y') ?? '—' }}</td><td>{{ $warrant?->is_valid ? 'Active Instructor' : 'Not Active' }}</td><td>@if($warrant)<a class="btn btn-sm" href="{{ route('portal.instructors.warrant',$warrant) }}">View Warrant</a>@endif</td></tr>@empty<tr><td colspan="6">No instructor records found.</td></tr>@endforelse
</tbody></table></div></div>{{ $cadets->links() }}
@endsection
