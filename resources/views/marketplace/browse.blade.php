<x-layouts.app title="Browse Parts">
    <section class="bg-zinc-950 px-4 py-10 text-white sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <h1 class="text-4xl font-black">Browse spare parts</h1>
            <p class="mt-2 text-zinc-300">Filter by part name, part number, car make, year, category, condition, location, and price.</p>
        </div>
    </section>
    <section class="mx-auto grid max-w-7xl gap-6 px-4 py-8 sm:px-6 lg:grid-cols-[300px_1fr] lg:px-8">
        <form class="h-fit rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 font-black">Search filters</h2>
            <div class="grid gap-4">
                <input name="q" value="{{ request('q') }}" class="rounded-md border border-zinc-300 px-3 py-2" placeholder="Part name, type, or number">
                <input name="part_number" value="{{ request('part_number') }}" class="rounded-md border border-zinc-300 px-3 py-2" placeholder="Part number / OEM number">
                <select name="make" data-car-make-select class="rounded-md border border-zinc-300 px-3 py-2"><option value="">Car make</option>@foreach($makes as $make)<option value="{{ $make->slug }}" @selected(request('make')===$make->slug)>{{ $make->name }}</option>@endforeach</select>
                <select name="model" data-car-model-select class="rounded-md border border-zinc-300 px-3 py-2">
                    <option value="">Car model</option>
                    @foreach($makes as $make)
                        <optgroup label="{{ $make->name }}">
                            @foreach($make->models as $model)
                                <option value="{{ $model->slug }}" data-make="{{ $make->slug }}" @selected(request('model')===$model->slug)>{{ $model->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <input name="year" value="{{ request('year') }}" class="rounded-md border border-zinc-300 px-3 py-2" placeholder="Year">
                <select name="category" class="rounded-md border border-zinc-300 px-3 py-2"><option value="">Category</option>@foreach($categories as $category)<option value="{{ $category->slug }}" @selected(request('category')===$category->slug)>{{ $category->name }}</option>@endforeach</select>
                <select name="condition" class="rounded-md border border-zinc-300 px-3 py-2"><option value="">Condition</option>@foreach(['New','Used','Refurbished'] as $condition)<option @selected(request('condition')===$condition)>{{ $condition }}</option>@endforeach</select>
                <input name="location" value="{{ request('location') }}" class="rounded-md border border-zinc-300 px-3 py-2" placeholder="Location">
                <div class="grid grid-cols-2 gap-2"><input name="min_price" value="{{ request('min_price') }}" class="rounded-md border border-zinc-300 px-3 py-2" placeholder="Min"><input name="max_price" value="{{ request('max_price') }}" class="rounded-md border border-zinc-300 px-3 py-2" placeholder="Max"></div>
                <button class="rounded-md bg-red-600 px-4 py-3 font-extrabold text-white">Apply Filters</button>
            </div>
        </form>
        <div>
            <div class="mb-4 flex items-center justify-between"><p class="font-bold">{{ $products->total() }} parts found</p><a class="text-sm font-bold text-red-600" href="{{ route('parts.index') }}">Clear filters</a></div>
            <div class="grid grid-cols-2 gap-3 sm:gap-5 xl:grid-cols-3">
                @forelse($products as $product)
                    @include('marketplace._product-card', ['product' => $product])
                @empty
                    <div class="rounded-lg bg-white p-8 text-center sm:col-span-2 xl:col-span-3">No matching parts yet.</div>
                @endforelse
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        </div>
    </section>
</x-layouts.app>
