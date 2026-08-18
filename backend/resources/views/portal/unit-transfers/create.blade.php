@extends('layouts.portal')
@section('content')
<div class="page-header"><div><p class="eyebrow">Personnel movement</p><h1>Request Unit Transfer</h1><p>A transfer is completed only after release, acceptance and verified payment.</p></div></div>
<div class="form-card"><form method="POST" action="{{ route('portal.unit-transfers.store') }}">@csrf
<label for="cadet_id">Cadet</label><select id="cadet_id" name="cadet_id" required>@foreach($cadets as $cadet)<option value="{{ $cadet->id }}">{{ $cadet->first_name }} {{ $cadet->last_name }} — {{ $cadet->unit->name }}</option>@endforeach</select>
<label for="to_unit_id">Destination Unit</label><select id="to_unit_id" name="to_unit_id" required><option value="">Select unit</option>@foreach($units as $unit)<option value="{{ $unit->id }}">{{ $unit->name }}</option>@endforeach</select>
<label for="reason">Reason</label><textarea id="reason" name="reason" rows="5" maxlength="2000"></textarea>
<div class="actions"><a class="btn" href="{{ route('portal.unit-transfers.index') }}">Cancel</a><button class="btn btn-primary" type="submit">Submit Application</button></div>
</form></div>
@endsection
