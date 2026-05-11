<x-layouts.app title="Spare Parts Marketplace">
    <section class="relative overflow-hidden text-white" style="background: radial-gradient(circle at 18% 18%, rgba(250, 204, 21, 0.18), transparent 22%), radial-gradient(circle at 86% 48%, rgba(220, 38, 38, 0.42), transparent 34%), linear-gradient(135deg, #030303 0%, #18181b 48%, #450a0a 100%);">
        <img class="absolute inset-0 h-full w-full object-cover opacity-20 mix-blend-overlay" src="https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?auto=format&fit=crop&w=1600&q=80" alt="Automotive spare parts background">
        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/25 to-black/50"></div>
        <div class="relative mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
            <div class="mx-auto flex max-w-5xl flex-col items-center text-center">
                <span class="mb-2 w-fit rounded bg-yellow-400 px-3 py-1 text-xs font-extrabold uppercase text-zinc-950">{{ $homeContent['hero_badge'] }}</span>
                <h1 class="max-w-3xl text-2xl font-black leading-tight sm:text-3xl">{{ $homeContent['hero_title'] }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-300">{{ $homeContent['hero_subtitle'] }}</p>
                <form action="{{ route('parts.index') }}" class="mt-5 w-full rounded-lg border border-zinc-800 bg-white p-4 text-left text-zinc-950 shadow-2xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm font-semibold text-zinc-700">I want to:</span>
                        <label class="cursor-pointer rounded-md bg-yellow-400 px-4 py-2 text-sm font-extrabold text-zinc-950" data-search-mode-option data-prompt="Describe the problem you are experiencing" data-placeholder="e.g. Brakes squeaking, engine light on, AC not working...">
                            <input class="sr-only" type="radio" name="search_mode" value="problem" checked>
                            Describe my problem
                        </label>
                        <label class="cursor-pointer rounded-md bg-zinc-200 px-4 py-2 text-sm font-bold text-zinc-800" data-search-mode-option data-prompt="{{ $homeContent['part_prompt'] }}" data-placeholder="{{ $homeContent['part_placeholder'] }}">
                            <input class="sr-only" type="radio" name="search_mode" value="part">
                            Search by part name
                        </label>
                    </div>

                    <div class="my-4 border-t border-zinc-200"></div>

                    <div class="grid gap-4 lg:grid-cols-[1fr_1fr_1fr_auto]">
                        <label class="grid gap-2 text-sm font-semibold text-zinc-700">
                            Car Make
                            <select name="make" data-car-make-select class="rounded-md border border-zinc-300 bg-zinc-100 px-3 py-3 text-sm font-medium text-zinc-700 outline-none focus:border-red-600">
                                <option value="">Select make</option>
                                @foreach($makes as $make)
                                    <option value="{{ $make->slug }}">{{ $make->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-semibold text-zinc-700">
                            Model
                            <select name="model" data-car-model-select class="rounded-md border border-zinc-300 bg-zinc-100 px-3 py-3 text-sm font-medium text-zinc-700 outline-none focus:border-red-600">
                                <option value="">Select model</option>
                                @foreach($makes as $make)
                                    <optgroup label="{{ $make->name }}">
                                        @foreach($make->models as $model)
                                            <option value="{{ $model->slug }}" data-make="{{ $make->slug }}">{{ $model->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-semibold text-zinc-700">
                            Year
                            <select name="year" class="rounded-md border border-zinc-300 bg-zinc-100 px-3 py-3 text-sm font-medium text-zinc-700 outline-none focus:border-red-600">
                                <option value="">Select year</option>
                                @for($year = now()->year + 1; $year >= 1995; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </label>
                        <button class="mt-7 rounded-md bg-red-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg hover:bg-red-700">
                            Search
                        </button>
                    </div>

                    <label class="mt-4 grid gap-2 text-sm font-semibold text-zinc-700">
                        <span data-search-prompt>Describe the problem you are experiencing</span>
                        @php
                            $searchSuggestions = array_values(array_unique(array_merge(
                                $homeContent['common_issues'],
                                $categories->pluck('name')->all(),
                                ['Brake pads', 'Radiator', 'Alternator', 'Shock absorber', 'Headlight assembly', 'Rear bumper', 'Service kit', 'Automatic gearbox', 'Battery', 'Spark plugs', 'Oil filter', 'Air filter', 'Fuel pump', 'Side mirror', 'Tail light']
                            )));
                        @endphp
                        <div class="relative grid gap-3 rounded-md border border-zinc-300 bg-zinc-100 p-2 sm:grid-cols-[1fr_auto]">
                            <input name="q" data-search-input data-search-suggestions="{{ json_encode($searchSuggestions) }}" class="min-w-0 bg-transparent px-3 py-2 text-sm outline-none" placeholder="e.g. Brakes squeaking, engine light on, AC not working..." autocomplete="off">
                            <button type="button" data-search-suggestion-toggle class="rounded-md bg-yellow-400 px-4 py-2 text-sm font-extrabold text-zinc-950">Search</button>
                            <div data-search-suggestion-panel class="absolute left-2 right-2 top-full z-50 mt-2 hidden overflow-hidden rounded-md border border-zinc-200 bg-white text-sm shadow-xl sm:right-28"></div>
                        </div>
                    </label>

                    <p class="mt-2 text-sm text-zinc-600">{{ $homeContent['search_note'] }}</p>

                    <div class="my-4 border-t border-zinc-200"></div>

                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="font-semibold text-zinc-700">Common issues:</span>
                        @foreach($homeContent['common_issues'] as $issue)
                            <a href="{{ route('parts.index', ['q' => $issue]) }}" class="rounded-md bg-zinc-200 px-3 py-2 font-bold text-zinc-700 hover:bg-yellow-400 hover:text-zinc-950">{{ $issue }}</a>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="bg-white py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 text-center">
                <p class="text-sm font-extrabold uppercase text-red-600">{{ $homeContent['trust_eyebrow'] }}</p>
                <h2 class="mt-1 text-3xl font-black">{{ $homeContent['trust_title'] }}</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($homeContent['trust_cards'] as $card)
                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-5">
                        <h3 class="font-extrabold">{{ $card['title'] }}</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-600">{{ $card['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div><p class="text-sm font-extrabold uppercase text-red-600">{{ $homeContent['categories_eyebrow'] }}</p><h2 class="text-3xl font-black">{{ $homeContent['categories_title'] }}</h2></div>
            <a href="{{ route('parts.index') }}" class="font-bold text-red-600">Browse all</a>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($categories as $category)
                <a href="{{ route('parts.index', ['category' => $category->slug]) }}" class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm hover:border-red-600">
                    <div class="grid h-10 w-10 place-items-center rounded-md bg-zinc-100 text-2xl">
                        @if($category->icon_url)
                            <img class="h-8 w-8 object-contain" src="{{ $category->icon_url }}" alt="{{ $category->name }} icon">
                        @else
                            {{ $category->icon }}
                        @endif
                    </div>
                    <h3 class="mt-3 font-extrabold">{{ $category->name }}</h3>
                    <p class="mt-1 text-sm text-zinc-500">{{ $category->products_count }} active listings</p>
                </a>
            @endforeach
        </div>
    </section>

    <section class="bg-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-end justify-between gap-4">
                <div><p class="text-sm font-extrabold uppercase text-red-600">{{ $homeContent['trending_eyebrow'] }}</p><h2 class="text-3xl font-black">{{ $homeContent['trending_title'] }}</h2></div>
                <a href="{{ route('parts.index') }}" class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-bold text-white">View Marketplace</a>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                @foreach($featuredProducts as $product)
                    @include('marketplace._product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div>
            <h2 class="mb-5 text-2xl font-black">{{ $homeContent['shops_title'] }}</h2>
            <div class="grid gap-4">
                @foreach($featuredShops as $shop)
                    <div class="flex items-center justify-between rounded-lg border border-zinc-200 bg-white p-5">
                        <div><h3 class="font-extrabold">{{ $shop->name }}</h3><p class="text-sm text-zinc-600">{{ $shop->location }} · {{ $shop->products_count }} parts available</p></div>
                        <a class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-bold" href="{{ route('shops.index') }}">View shop</a>
                    </div>
                @endforeach
            </div>
        </div>
        <div>
            <h2 class="mb-5 text-2xl font-black">{{ $homeContent['garages_title'] }}</h2>
            <div class="grid gap-4">
                @foreach($featuredGarages as $garage)
                    <a href="{{ route('garages.show', $garage) }}" class="block rounded-lg border border-zinc-200 bg-white p-5 transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex items-center justify-between"><h3 class="font-extrabold">{{ $garage->name }}</h3><span class="font-bold text-yellow-600">★ {{ number_format((float) $garage->rating, 1) }}/5</span></div>
                        <p class="mt-1 text-sm text-zinc-600">{{ $garage->location }} · {{ implode(', ', array_slice($garage->services ?? [], 0, 3)) }}</p>
                        <p class="mt-1 text-xs font-bold text-red-600">Specializes in {{ $garage->specializationLabel() }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-red-600 px-4 py-12 text-white">
        <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-5 sm:flex-row sm:items-center">
            <div><h2 class="text-3xl font-black">{{ $homeContent['cta_title'] }}</h2><p class="mt-2 text-red-50">{{ $homeContent['cta_text'] }}</p></div>
            <a href="{{ route('sell') }}" class="rounded-md bg-white px-6 py-3 font-extrabold text-red-600">{{ $homeContent['cta_button'] }}</a>
        </div>
    </section>
</x-layouts.app>
