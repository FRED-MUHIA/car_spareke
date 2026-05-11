<x-layouts.app title="Pay {{ $plan->name }}">
    <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <p class="text-sm font-extrabold uppercase text-red-600">M-Pesa payment</p>
            <h1 class="mt-2 text-3xl font-black">{{ $plan->name }}</h1>
            <p class="mt-3 text-4xl font-black">KES {{ number_format($plan->price) }}<span class="text-base font-bold text-zinc-500">/{{ $plan->billing_period }}</span></p>

            <div class="mt-6 grid gap-3 rounded-md bg-zinc-50 p-4 text-sm text-zinc-700 sm:grid-cols-2">
                <p><b>Account:</b> {{ $user->name }}</p>
                <p><b>Account code:</b> {{ $user->account_code }}</p>
                <p><b>Email:</b> {{ $user->email }}</p>
                <p><b>Current phone:</b> {{ $user->phone }}</p>
            </div>

            @if($isTokened)
                <div class="mt-6 rounded-md bg-green-50 p-4 text-sm font-bold text-green-700">
                    Status: <span class="rounded bg-green-600 px-2 py-1 text-white">Tokened</span>
                </div>
            @else
                <form method="POST" action="{{ route('plans.stk', $plan) }}" class="mt-6 grid gap-4">
                    @csrf
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        M-Pesa phone number
                        <input name="phone" value="{{ old('phone', $user->phone) }}" class="rounded-md border border-zinc-300 px-3 py-3 text-base font-normal text-zinc-950" placeholder="+2547..." required>
                    </label>
                    @if($errors->any())<p class="text-sm font-bold text-red-600">{{ $errors->first() }}</p>@endif
                    <button class="rounded-md bg-red-600 px-5 py-3 font-extrabold text-white">Pay Now with M-Pesa STK</button>
                </form>
            @endif

            @if($latestPayment)
                <div class="mt-6 rounded-md border border-zinc-200 p-4 text-sm">
                    <b>Latest payment:</b>
                    {{ $latestPayment->plan?->name }} · KES {{ number_format($latestPayment->amount) }} ·
                    @if($latestPayment->status === 'paid')
                        <span class="font-extrabold text-green-700">Tokened</span>
                    @else
                        {{ str_replace('_', ' ', $latestPayment->status) }}
                    @endif
                    @if($latestPayment->response_description)
                        <p class="mt-1 text-zinc-600">{{ $latestPayment->response_description }}</p>
                    @endif
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>
