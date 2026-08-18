@extends('layouts.portal')
@section('content')
<div class="page-header"><div><p class="eyebrow">Personnel movement</p><h1>Unit Transfers</h1><p>Review and manage cadet unit transfer applications.</p></div><a class="btn btn-primary" href="{{ route('portal.unit-transfers.create') }}">New Transfer</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>Reference</th><th>Cadet</th><th>From</th><th>To</th><th>Status</th><th>Applied</th><th>Actions</th></tr></thead><tbody>
@forelse($transfers as $transfer)<tr><td>#UT-{{ str_pad((string)$transfer->id, 6, '0', STR_PAD_LEFT) }}</td><td>{{ $transfer->cadet->first_name }} {{ $transfer->cadet->last_name }}</td><td>{{ $transfer->fromUnit->name }}</td><td>{{ $transfer->toUnit->name }}</td><td><span class="status status-{{ $transfer->status }}">{{ str_replace('_',' ',ucfirst($transfer->status)) }}</span></td><td>{{ optional($transfer->applied_at)->format('d M Y') }}</td><td><div class="actions">@if($transfer->status === 'pending_release')<form method="POST" action="{{ route('portal.unit-transfers.release',$transfer) }}">@csrf<button class="btn btn-sm" type="submit">Release</button></form>@elseif($transfer->status === 'released')<form method="POST" action="{{ route('portal.unit-transfers.accept',$transfer) }}">@csrf<button class="btn btn-sm btn-primary" type="submit">Accept</button></form>@else<span>—</span>@endif</div></td></tr>@empty<tr><td colspan="7">No unit transfer applications found.</td></tr>@endforelse
</tbody></table></div></div>
{{ $transfers->links() }}
@endsection
