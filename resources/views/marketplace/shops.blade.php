<x-layouts.app title="Find Shops">
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-black">Find spare parts shops</h1>
        <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach($shops as $shop)
                <article class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <img class="h-16 w-16 rounded-md object-cover" src="{{ $shop->logo_path ?: 'https://images.unsplash.com/photo-1486006920555-c77dcf18193c?auto=format&fit=crop&w=300&q=80' }}" alt="{{ $shop->name }}">
                        <div><h2 class="font-black">{{ $shop->name }}</h2><p class="text-sm text-zinc-600">{{ $shop->location }}</p></div>
                    </div>
                    <p class="mt-4 text-sm text-zinc-600">{{ $shop->description }}</p>
                    <div class="mt-5 flex items-center justify-between"><span class="font-bold">{{ $shop->products_count }} parts available</span><a class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-bold text-white" href="tel:{{ $shop->phone }}">Contact</a></div>
                </article>
            @endforeach
        </div>
    </section>
</x-layouts.app>
