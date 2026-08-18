@extends('layouts.portal')
@section('content')
<div class="page-header"><div><p class="eyebrow">Ward movement</p><h1>Ward Transfers</h1><p>National approval is mandatory before a cadet's ward can change.</p></div>@if(!$user->isAdmin() && $user->cadet)<a class="btn btn-primary" href="{{ route('portal.ward-transfers.create') }}">Apply for Transfer</a>@endif</div>
@if(session('success'))<div class="alert alert-success">{{session('success')}}</div>@endif
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>Reference</th><th>Cadet</th><th>Current Ward</th><th>Destination</th><th>Route</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($transfers as $t)<tr><td>{{$t->reference}}</td><td>{{$t->cadet->first_name}} {{$t->cadet->last_name}}</td><td>{{$t->fromWard->name}}</td><td>{{$t->toWard->name}}</td><td>{{$t->fromWard->lga->name}}, {{$t->fromWard->lga->state->name}} → {{$t->toWard->lga->name}}, {{$t->toWard->lga->state->name}}</td><td>{{str_replace('_',' ',ucfirst($t->status))}}</td><td>
@php $actions=['pending_source_hcs'=>['release','Release by HCS'], 'pending_source_lga'=>['source-lga','Acknowledge LGA'], 'pending_source_state'=>['source-state','Acknowledge State'], 'pending_destination_hcs'=>['destination-accept','Accept by HCS'], 'pending_destination_lga'=>['destination-lga','Acknowledge LGA'], 'pending_destination_state'=>['destination-state','Acknowledge State'], 'pending_national'=>['national-approve','Final National Approval']]; $a=$actions[$t->status]??null; @endphp
@if($a)<form method="POST" action="{{route('portal.ward-transfers.action',['transfer'=>$t,'action'=>$a[0]])}}">@csrf<button class="btn btn-sm btn-primary" type="submit">{{$a[1]}}</button></form>@else — @endif</td></tr>@empty<tr><td colspan="7">No ward transfer applications found.</td></tr>@endforelse
</tbody></table></div></div>{{ $transfers->links() }}
@endsection
