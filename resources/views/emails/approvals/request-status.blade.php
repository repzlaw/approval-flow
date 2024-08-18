@component('mail::message')
# Approval Request Status

Your Approval Request has been {{$status}}.

{{$comment}}

@component('mail::button', ['url' => url('/admin/approvals?activeTab=' . strtolower($status))])
View Approval
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent