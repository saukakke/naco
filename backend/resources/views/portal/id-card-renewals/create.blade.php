@extends('layouts.portal')
@section('content')
<div class="page-header"><div><p class="eyebrow">Identity management</p><h1>Renew ID Card</h1><p>Your current card expires on <strong>{{ $cadet->id_card_expires_at->format('d M Y') }}</strong>. You are within the permitted two-month renewal window.</p></div></div>
<div class="form-card"><form method="POST" action="{{ route('portal.id-card-renewals.store') }}">@csrf<label for="reason">Reason (optional)</label><textarea id="reason" name="reason" rows="5" maxlength="2000"></textarea><div class="actions"><a class="btn" href="{{ route('portal.id-card-renewals.index') }}">Cancel</a><button class="btn btn-primary" type="submit">Submit Renewal Application</button></div></form></div>
@endsection
