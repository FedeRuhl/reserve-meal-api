@component('mail::message')
# Verification code

Please use this code to change your password.
Don't share it with anyone.

<b> {{ $mailData }} </b>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
