@extends('layouts.portal')
@section('content')
<div class="page-header"><div><p class="eyebrow">System security</p><h1>Audit Trail</h1><p>Administrative and workflow actions recorded for accountability.</p></div></div>
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>Date</th><th>User</th><th>Action</th><th>Description</th><th>IP</th></tr></thead><tbody>@forelse($logs as $log)<tr><td>{{ $log->created_at->format('d M Y H:i:s') }}</td><td>{{ $log->user?->name ?? 'System' }}</td><td>{{ $log->action }}</td><td>{{ $log->description }}</td><td>{{ $log->ip_address ?? '—' }}</td></tr>@empty<tr><td colspan="5">No audit records.</td></tr>@endforelse</tbody></table></div></div>{{ $logs->links() }}
@endsection
