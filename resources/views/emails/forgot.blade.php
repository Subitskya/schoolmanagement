@component('mail::message')
Hello {{ $user->name }},

<p>We understand it happens. Please click the button below to reset your password.</p>

@component('mail::button', ['url' => url('reset/' . $user->remember_token)])
Reset Password
@endcomponent

If you did not request a password reset, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
