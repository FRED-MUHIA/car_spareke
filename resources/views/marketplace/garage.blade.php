<x-layouts.app title="{{ $garage->name }}">
    <section class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.1fr_.9fr] lg:px-8">
        <div>
            <img class="aspect-[16/10] w-full rounded-lg object-cover shadow-sm" src="{{ $garage->image() }}" alt="{{ $garage->name }}">
            <div class="mt-6 rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black">About this garage</h2>
                <p class="mt-4 leading-7 text-zinc-700">{{ $garage->description }}</p>
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach($garage->services ?? [] as $service)
                        <span class="rounded bg-zinc-100 px-3 py-2 text-sm font-bold">{{ $service }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <aside class="h-fit rounded-lg border border-zinc-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-extrabold uppercase text-red-600">Garage details</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <h1 class="text-3xl font-black">{{ $garage->name }}</h1>
                        @if($garage->isVerified())
                            <span class="rounded bg-blue-600 px-2 py-1 text-xs font-black uppercase text-white">Verified</span>
                        @else
                            <span class="rounded bg-zinc-500 px-2 py-1 text-xs font-black uppercase text-white">Not verified</span>
                        @endif
                    </div>
                </div>
                <span class="rounded bg-yellow-400 px-3 py-2 text-sm font-black">★ {{ number_format((float) $garage->rating, 1) }}</span>
            </div>

            <div class="mt-6 grid gap-3 text-sm text-zinc-700">
                <p><b>Location:</b> {{ $garage->location }}</p>
                <p><b>Specialization:</b> {{ $garage->specializationLabel() }}</p>
                <p><b>Reviews:</b> {{ $garage->review_count }} {{ Illuminate\Support\Str::plural('review', $garage->review_count) }}</p>
                <p><b>Phone:</b> {{ $garage->phone }}</p>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                <a class="rounded-md bg-red-600 px-4 py-3 text-center font-extrabold text-white" href="tel:{{ $garage->phone }}">Call garage</a>
                <a class="rounded-md bg-zinc-950 px-4 py-3 text-center font-extrabold text-white" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($garage->location) }}" target="_blank" rel="noopener">Open map</a>
            </div>
        </aside>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h2 class="text-2xl font-black">Customer reviews</h2>
            <div class="mt-5 grid gap-3 md:grid-cols-2">
                @forelse($garage->reviews as $review)
                    <div class="rounded-md bg-zinc-50 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <b class="text-sm">{{ $review->reviewer_name }}</b>
                            <span class="text-sm font-black text-yellow-600">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                        </div>
                        <p class="mt-2 text-sm leading-6 text-zinc-600">{{ $review->comment }}</p>
                    </div>
                @empty
                    <p class="rounded-md bg-zinc-50 p-4 text-sm text-zinc-500">No written reviews yet.</p>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.app>
