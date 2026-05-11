<h1>Seller account approved</h1>

<p>Hello {{ $user->name }},</p>

<p>Your seller account has been approved. You can now log in and start publishing spare part listings.</p>

<p><a href="{{ route('login') }}">Log in to your account</a></p>

<p>Thank you,<br>{{ config('app.name') }}</p>
