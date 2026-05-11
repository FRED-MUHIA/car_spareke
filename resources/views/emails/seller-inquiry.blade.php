<h1>New buyer inquiry</h1>

<p>Hello,</p>

<p>You have a request from a buyer interested in your listing. Please attend to them as soon as possible.</p>

<p><b>Part:</b> {{ $inquiry->product?->title }}</p>

@if($inquiry->product?->part_number)
    <p><b>Part number:</b> {{ $inquiry->product->part_number }}</p>
@endif

<p><b>Customer name:</b> {{ $inquiry->customer_name }}</p>
<p><b>Customer phone:</b> {{ $inquiry->customer_phone }}</p>

@if($inquiry->customer_email)
    <p><b>Customer email:</b> {{ $inquiry->customer_email }}</p>
@endif

<p><b>Message:</b></p>
<p>{{ $inquiry->message }}</p>

<p>Thank you,<br>Car Spares Sales</p>
