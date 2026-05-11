<x-layouts.app title="Pricing">
    <section class="bg-zinc-950 px-4 py-12 text-white sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl"><h1 class="text-4xl font-black">Seller pricing packages</h1><p class="mt-3 text-zinc-300">Start free, upgrade for more listings, featured products, shop profiles, and promoted ads.</p></div>
    </section>
    <section class="mx-auto grid max-w-7xl items-stretch gap-5 px-4 py-10 sm:px-6 lg:grid-cols-4 lg:px-8">
        @foreach($plans as $plan)
            <article class="flex h-full flex-col rounded-lg border {{ $plan->is_featured ? 'border-red-600 ring-2 ring-red-600' : 'border-zinc-200' }} bg-white p-5 shadow-sm">
                <div class="h-7">
                    @if($plan->is_featured)<span class="inline-flex rounded bg-yellow-400 px-2 py-1 text-xs font-black">Best value</span>@endif
                </div>
                <h2 class="mt-3 flex min-h-16 items-start text-2xl font-black leading-tight">{{ $plan->name }}</h2>
                <p class="mt-2 flex min-h-12 items-end text-4xl font-black leading-none">KES {{ number_format($plan->price) }}<span class="pb-1 text-sm font-bold text-zinc-500">/{{ $plan->billing_period }}</span></p>
                <ul class="mt-5 grid min-h-36 content-start gap-2 text-sm leading-snug">@foreach($plan->features ?? [] as $feature)<li class="flex gap-2"><span class="font-black text-red-600">✓</span><span>{{ $feature }}</span></li>@endforeach</ul>
                <a class="mt-4 block rounded-md bg-zinc-950 px-4 py-3 text-center font-extrabold text-white hover:bg-red-600" href="{{ route('plans.select', $plan) }}">{{ $plan->cta_label }}</a>
            </article>
        @endforeach
    </section>
</x-layouts.app>
