<x-layouts.app title="{{ $product->title }}">
    <section class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.1fr_.9fr] lg:px-8">
        <div>
            <div class="overflow-hidden rounded-lg bg-white">
                <img class="aspect-[16/10] w-full object-cover" src="{{ $product->image() }}" alt="{{ $product->title }}">
            </div>
            <div class="mt-4 grid grid-cols-3 gap-3">
                @foreach($product->imageList() as $image)
                    <img class="aspect-[4/3] rounded-lg object-cover" src="{{ $image }}" alt="{{ $product->title }}">
                @endforeach
            </div>
        </div>
        <aside class="h-fit rounded-lg border border-zinc-200 bg-white p-6 shadow-sm">
            <p class="font-extrabold uppercase text-red-600">{{ $product->category->name }} · {{ $product->part_type }}</p>
            <h1 class="mt-2 text-3xl font-black">{{ $product->title }}</h1>
            <p class="mt-4 text-3xl font-black">KES {{ number_format($product->price) }}</p>
            <div class="mt-5 grid gap-3 text-sm text-zinc-700">
                <p><b>Compatible:</b> {{ $product->make?->name }} {{ $product->model?->name }}</p>
                @if($product->part_number)
                    <p><b>Part number:</b> {{ $product->part_number }}</p>
                @endif
                <p><b>Year range:</b> {{ $product->year_from }}-{{ $product->year_to }}</p>
                <p><b>Condition:</b> {{ $product->condition }}</p>
                <p><b>Location:</b> {{ $product->location }}</p>
                <p><b>Seller:</b> {{ $product->shop?->name ?? $product->seller_name }}</p>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-3">
                <a class="rounded-md bg-red-600 px-4 py-3 text-center font-extrabold text-white" href="tel:{{ $product->seller_phone }}">Call seller</a>
                <a class="rounded-md bg-zinc-950 px-4 py-3 text-center font-extrabold text-white" href="{{ $product->whatsappUrl() }}">WhatsApp</a>
            </div>
        </aside>
    </section>
    <section class="mx-auto grid max-w-7xl gap-8 px-4 pb-12 sm:px-6 lg:grid-cols-[1fr_380px] lg:px-8">
        <div class="rounded-lg bg-white p-6">
            <h2 class="text-2xl font-black">Description</h2>
            <p class="mt-4 leading-7 text-zinc-700">{{ $product->description }}</p>
        </div>
        <form method="POST" action="{{ route('parts.inquiry', $product) }}" class="rounded-lg bg-zinc-950 p-6 text-white">
            @csrf
            <h2 class="text-xl font-black">Contact seller</h2>
            <div class="mt-4 grid gap-3">
                <input name="customer_name" class="rounded-md bg-white px-3 py-2 text-zinc-950" placeholder="Your name" required>
                <input name="customer_phone" class="rounded-md bg-white px-3 py-2 text-zinc-950" placeholder="Phone number" required>
                <input name="customer_email" class="rounded-md bg-white px-3 py-2 text-zinc-950" placeholder="Email optional">
                <textarea name="message" class="rounded-md bg-white px-3 py-2 text-zinc-950" rows="4" required>I am interested in {{ $product->title }}.</textarea>
                <button class="rounded-md bg-yellow-400 px-4 py-3 font-extrabold text-zinc-950">Send Inquiry</button>
            </div>
        </form>
    </section>
    <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8" data-similar-slider>
        <div class="mb-5 flex items-center justify-between gap-4">
            <h2 class="text-2xl font-black">Similar parts</h2>
            @if($similar->isNotEmpty())
                <div class="flex gap-2">
                    <button type="button" class="grid h-10 w-10 place-items-center rounded-md border border-zinc-300 bg-white text-xl font-black hover:border-red-600 hover:text-red-600" data-similar-prev aria-label="Previous similar parts">&lsaquo;</button>
                    <button type="button" class="grid h-10 w-10 place-items-center rounded-md border border-zinc-300 bg-white text-xl font-black hover:border-red-600 hover:text-red-600" data-similar-next aria-label="Next similar parts">&rsaquo;</button>
                </div>
            @endif
        </div>

        @if($similar->isNotEmpty())
            <div class="-mx-4 overflow-hidden px-4">
                <div class="flex snap-x snap-mandatory gap-3 overflow-x-auto scroll-smooth pb-4 sm:gap-5" data-similar-track>
                    @foreach($similar as $similarProduct)
                        <div class="min-w-[78%] snap-start sm:min-w-[42%] lg:min-w-[24%]">
                            @include('marketplace._product-card', ['product' => $similarProduct])
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="rounded-lg bg-white p-8 text-center font-semibold text-zinc-600">No similar parts available yet.</div>
        @endif
    </section>
</x-layouts.app>
