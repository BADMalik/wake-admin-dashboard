@component('mail::message')
# {{ config('app.name') }} Password Reset

Use the provided OTP to reset your Password <br>
OTP: <b>{{$OTP}}</b>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
