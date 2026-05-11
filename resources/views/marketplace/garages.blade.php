<x-layouts.app title="Find Garages">
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-black">Find garages and mechanics</h1>
        <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach($garages as $garage)
                <article class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <a href="{{ route('garages.show', $garage) }}" class="block">
                        <img class="mb-4 aspect-[16/9] w-full rounded-md object-cover" src="{{ $garage->image() }}" alt="{{ $garage->name }}">
                        <div class="flex items-start justify-between gap-4"><div><div class="flex flex-wrap items-center gap-2"><h2 class="font-black">{{ $garage->name }}</h2>@if($garage->isVerified())<span class="rounded bg-blue-600 px-2 py-1 text-[10px] font-black uppercase text-white">Verified</span>@endif</div><p class="text-sm text-zinc-600">{{ $garage->location }}</p></div><span class="rounded bg-yellow-400 px-2 py-1 text-sm font-black">★ {{ number_format((float) $garage->rating, 1) }}</span></div>
                        <p class="mt-4 text-sm text-zinc-600">{{ $garage->description }}</p>
                        <p class="mt-3 text-sm font-bold text-zinc-800">Specialization: <span class="text-red-600">{{ $garage->specializationLabel() }}</span></p>
                        <div class="mt-4 flex flex-wrap gap-2">@foreach($garage->services ?? [] as $service)<span class="rounded bg-zinc-100 px-2 py-1 text-xs font-bold">{{ $service }}</span>@endforeach</div>
                    </a>
                    <div class="mt-5 flex items-center justify-between">
                        <button type="button" class="text-sm font-bold text-zinc-950 hover:text-red-600" data-review-modal-open>
                            {{ $garage->review_count }} {{ Illuminate\Support\Str::plural('review', $garage->review_count) }}
                        </button>
                        <a class="text-sm font-bold text-red-600" href="{{ route('garages.show', $garage) }}">View details</a>
                        <a class="rounded-md bg-red-600 px-4 py-2 text-sm font-bold text-white" href="tel:{{ $garage->phone }}">Contact</a>
                    </div>
                    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4 py-8" data-review-modal>
                        <div class="max-h-full w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-5 shadow-2xl">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="text-xl font-black">{{ $garage->name }} reviews</h2>
                                    <p class="mt-1 text-sm text-zinc-600">{{ $garage->rating }} average · {{ $garage->review_count }} {{ Illuminate\Support\Str::plural('review', $garage->review_count) }}</p>
                                </div>
                                <button type="button" class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-black" data-review-modal-close>Close</button>
                            </div>

                            <div class="mt-5 grid gap-3">
                                @forelse($garage->reviews as $review)
                                    <div class="rounded-md bg-zinc-50 p-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <b class="text-sm">{{ $review->reviewer_name }}</b>
                                            <span class="text-sm font-black text-yellow-600">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                                        </div>
                                        <p class="mt-2 text-sm leading-6 text-zinc-600">{{ $review->comment }}</p>
                                    </div>
                                @empty
                                    <p class="rounded-md bg-zinc-50 p-3 text-sm text-zinc-500">No written reviews yet.</p>
                                @endforelse
                            </div>

                            <form method="POST" action="{{ route('garages.reviews.store', $garage) }}" class="mt-4 grid gap-3 rounded-md border border-zinc-200 p-4">
                                @csrf
                                <h3 class="font-black">Post a review</h3>
                                <input name="reviewer_name" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" placeholder="Your name" required>
                                <select name="rating" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" required>
                                    <option value="">Rating</option>
                                    @for($rating = 5; $rating >= 1; $rating--)
                                        <option value="{{ $rating }}">{{ $rating }} star{{ $rating === 1 ? '' : 's' }}</option>
                                    @endfor
                                </select>
                                <textarea name="comment" rows="3" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" placeholder="Share your experience" required></textarea>
                                <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-bold text-white hover:bg-red-600">Submit Review</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</x-layouts.app>
