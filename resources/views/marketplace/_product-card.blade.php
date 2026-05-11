<article class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
    <a href="{{ route('parts.show', $product) }}" class="block">
        <div class="relative aspect-[4/3] bg-zinc-200">
            <img class="h-full w-full object-cover" src="{{ $product->image() }}" alt="{{ $product->title }}">
            <span class="absolute left-2 top-2 rounded {{ $product->condition === 'New' ? 'bg-emerald-600' : 'bg-red-600' }} px-2 py-1 text-[10px] font-extrabold text-white sm:left-3 sm:top-3 sm:text-xs">{{ $product->condition }}</span>
            @if($product->is_featured)<span class="absolute right-2 top-2 rounded bg-yellow-400 px-2 py-1 text-[10px] font-extrabold text-zinc-950 sm:right-3 sm:top-3 sm:text-xs">Featured</span>@endif
        </div>
    </a>
    <div class="grid gap-2 p-3 sm:gap-3 sm:p-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-wide text-red-600 sm:text-xs">{{ $product->category->name }}</p>
            <h3 class="mt-1 line-clamp-2 text-sm font-extrabold sm:text-base">{{ $product->title }}</h3>
        </div>
        <div class="line-clamp-3 text-xs text-zinc-600 sm:text-sm">
            <p>{{ $product->make?->name }} {{ $product->model?->name }} · {{ $product->year_from }}-{{ $product->year_to }}</p>
            <p>{{ $product->shop?->name ?? $product->seller_name }} · {{ $product->location }}</p>
        </div>
        <div class="grid gap-2">
            <strong class="text-sm text-zinc-950 sm:text-lg">KES {{ number_format($product->price) }}</strong>
            <div class="flex flex-wrap gap-2">
                <span class="w-fit rounded bg-zinc-100 px-2 py-1 text-[10px] font-bold sm:text-xs">{{ $product->part_type }}</span>
                @if($product->part_number)
                    <span class="w-fit rounded bg-red-50 px-2 py-1 text-[10px] font-bold text-red-700 sm:text-xs">No: {{ $product->part_number }}</span>
                @endif
            </div>
        </div>
        <div class="grid gap-2 sm:grid-cols-2">
            <a class="rounded-md border border-zinc-300 px-2 py-2 text-center text-xs font-bold hover:border-red-600 sm:px-3 sm:text-sm" href="{{ route('parts.show', $product) }}">View</a>
            <a class="rounded-md bg-zinc-950 px-2 py-2 text-center text-xs font-bold text-white hover:bg-red-600 sm:px-3 sm:text-sm" href="{{ $product->whatsappUrl() }}">WhatsApp</a>
        </div>
    </div>
</article>
