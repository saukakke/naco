@extends('layouts.portal')
@section('content')
<div class="page-header"><div><p class="eyebrow">Updates</p><h1>Notifications</h1><p>Approval, payment, training, transfer and personnel updates.</p></div></div>
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>Notification</th><th>Message</th><th>Date</th><th></th></tr></thead><tbody>@forelse($notifications as $notification)<tr><td><strong>{{ $notification->title }}</strong></td><td>{{ $notification->message }}</td><td>{{ $notification->created_at->format('d M Y H:i') }}</td><td>@if(!$notification->read_at)<form method="POST" action="{{ route('portal.notifications.read',$notification) }}">@csrf<button class="btn btn-sm" type="submit">Mark read</button></form>@else Read @endif</td></tr>@empty<tr><td colspan="4">No notifications.</td></tr>@endforelse</tbody></table></div></div>{{ $notifications->links() }}
@endsection
