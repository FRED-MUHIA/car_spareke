<x-layouts.app title="Seller Dashboard">
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-extrabold uppercase text-red-600">Seller workspace</p>
                <h1 class="mt-1 text-4xl font-black">Seller dashboard</h1>
                <p class="mt-2 text-zinc-600">Manage your shop profile, listings, sold parts, and customer inquiries.</p>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm font-bold">
                    <span class="rounded bg-zinc-100 px-3 py-1">Account code: {{ Auth::user()->account_code }}</span>
                    <span class="rounded bg-zinc-100 px-3 py-1">Plan: {{ $selectedPlan?->name ?? 'Free Listing' }}</span>
                    @if($selectedPlan && (float) $selectedPlan->price > 0)
                        @if($latestPlanPayment?->status === 'paid')
                            <span class="rounded bg-green-600 px-3 py-1 text-white">Tokened</span>
                        @else
                            <a class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700" href="{{ route('plans.pay', $selectedPlan) }}">Pay Now</a>
                        @endif
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a class="rounded-md border border-zinc-300 bg-white px-5 py-3 font-extrabold text-zinc-950" href="{{ route('parts.index') }}">View Marketplace</a>
                <a class="rounded-md {{ $freeListingLimitReached ? 'bg-zinc-950' : 'bg-red-600' }} px-5 py-3 font-extrabold text-white" href="{{ $freeListingLimitReached ? route('pricing') : route('sell') }}">{{ $freeListingLimitReached ? 'Upgrade Plan' : 'Add Part' }}</a>
            </div>
        </div>

        @if($freeListingLimitReached)
            <div class="mt-6 rounded-lg border border-yellow-300 bg-yellow-50 p-5">
                <p class="text-sm font-extrabold uppercase text-red-600">Free listing limit reached</p>
                <h2 class="mt-1 text-2xl font-black">You have used all {{ $freeListingLimit }} free active listings.</h2>
                <p class="mt-2 text-zinc-700">Upgrade your seller package to add more parts, promote listings, and keep receiving buyer inquiries.</p>
                <a class="mt-4 inline-flex rounded-md bg-zinc-950 px-5 py-3 font-extrabold text-white hover:bg-red-600" href="{{ route('pricing') }}">Upgrade now</a>
            </div>
        @endif

        <div class="mt-8 grid gap-5 md:grid-cols-4">
            <div class="rounded-lg bg-white p-5 shadow-sm"><p class="text-sm font-bold text-zinc-500">All listings</p><b class="text-3xl">{{ $totalCount }}</b></div>
            <div class="rounded-lg bg-white p-5 shadow-sm"><p class="text-sm font-bold text-zinc-500">Active listings</p><b class="text-3xl">{{ $activeCount }}</b></div>
            <div class="rounded-lg bg-white p-5 shadow-sm"><p class="text-sm font-bold text-zinc-500">Sold listings</p><b class="text-3xl">{{ $soldCount }}</b></div>
            <div class="rounded-lg bg-white p-5 shadow-sm"><p class="text-sm font-bold text-zinc-500">Buyer inquiries</p><b class="text-3xl">{{ $inquiryCount }}</b></div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[380px_1fr]">
            <div class="grid gap-6">
                <div class="rounded-lg bg-zinc-950 p-5 text-white shadow-sm">
                    <p class="text-sm font-extrabold uppercase text-yellow-400">Shop status</p>
                    <h2 class="mt-2 text-2xl font-black">{{ $shop?->name ?? 'No shop profile yet' }}</h2>
                    <p class="mt-2 text-sm text-zinc-300">{{ $shop ? $shop->location.' · '.$shop->status : 'Create a shop profile to build buyer trust.' }}</p>
                    @if($shop?->logo_path)
                        <img class="mt-4 h-20 w-20 rounded-md object-cover" src="{{ $shop->logo_path }}" alt="{{ $shop->name }}">
                    @endif
                </div>

                <form method="POST" enctype="multipart/form-data" action="{{ route('seller.shop.store') }}" class="rounded-lg bg-white p-5 shadow-sm">
                    @csrf
                    <h2 class="text-xl font-black">Shop profile</h2>
                    <div class="mt-4 grid gap-3">
                        <input name="name" value="{{ old('name', $shop->name ?? '') }}" class="rounded-md border border-zinc-300 px-3 py-2" placeholder="Shop name" required>
                        <label class="grid gap-2 text-sm font-bold text-zinc-700">
                            Upload logo image
                            <input name="logo_image" type="file" accept="image/*,.webp,.svg" class="rounded-md border border-zinc-300 px-3 py-2 text-base font-normal text-zinc-950">
                        </label>
                        <input name="logo_path" value="{{ old('logo_path', $shop->logo_path ?? '') }}" class="rounded-md border border-zinc-300 px-3 py-2" placeholder="Logo URL/path">
                        <label class="grid gap-2 text-sm font-bold text-zinc-700">
                            Upload banner image
                            <input name="banner_image" type="file" accept="image/*,.webp" class="rounded-md border border-zinc-300 px-3 py-2 text-base font-normal text-zinc-950">
                        </label>
                        <input name="banner_path" value="{{ old('banner_path', $shop->banner_path ?? '') }}" class="rounded-md border border-zinc-300 px-3 py-2" placeholder="Banner URL/path">
                        <input name="location" value="{{ old('location', $shop->location ?? Auth::user()->location) }}" class="rounded-md border border-zinc-300 px-3 py-2" placeholder="Location" required>
                        <input name="phone" value="{{ old('phone', $shop->phone ?? Auth::user()->phone) }}" class="rounded-md border border-zinc-300 px-3 py-2" placeholder="Phone" required>
                        <input name="whatsapp" value="{{ old('whatsapp', $shop->whatsapp ?? Auth::user()->phone) }}" class="rounded-md border border-zinc-300 px-3 py-2" placeholder="WhatsApp">
                        <input name="email" value="{{ old('email', $shop->email ?? Auth::user()->email) }}" class="rounded-md border border-zinc-300 px-3 py-2" placeholder="Email">
                        <textarea name="description" class="rounded-md border border-zinc-300 px-3 py-2" rows="4" placeholder="Shop description">{{ old('description', $shop->description ?? '') }}</textarea>
                        <button class="rounded-md bg-zinc-950 px-4 py-3 font-extrabold text-white">Save Shop</button>
                    </div>
                </form>
            </div>

            <div class="grid gap-8">
                <div class="rounded-lg bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <h2 class="text-xl font-black">Listings manager</h2>
                        <a class="rounded-md bg-red-600 px-4 py-2 text-sm font-extrabold text-white" href="{{ route('sell') }}">New Listing</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left text-sm">
                            <thead>
                                <tr class="border-b text-zinc-500"><th class="py-3">Part</th><th>Compatibility</th><th>Price</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                            @forelse($products as $product)
                                <tr class="border-b align-top">
                                    <td class="py-3 font-bold">{{ $product->title }}<br><span class="font-normal text-zinc-500">{{ $product->category->name }} · {{ $product->location }}@if($product->part_number) · No: {{ $product->part_number }}@endif</span></td>
                                    <td>{{ $product->make?->name }} {{ $product->model?->name }}<br><span class="text-zinc-500">{{ $product->year_from }}-{{ $product->year_to }}</span></td>
                                    <td>KES {{ number_format($product->price) }}</td>
                                    <td><span class="rounded bg-zinc-100 px-2 py-1 font-bold">{{ $product->status }}</span></td>
                                    <td class="flex flex-wrap gap-2 py-3">
                                        <a class="rounded border px-3 py-1 font-bold" href="{{ route('parts.show', $product) }}">View</a>
                                        <a class="rounded border px-3 py-1 font-bold" href="{{ route('seller.products.edit', $product) }}">Edit</a>
                                        <form method="POST" action="{{ route('seller.products.sold', $product) }}">@csrf @method('PATCH')<button class="rounded border px-3 py-1 font-bold">Sold</button></form>
                                        <form method="POST" action="{{ route('seller.products.destroy', $product) }}">@csrf @method('DELETE')<button class="rounded border border-red-300 px-3 py-1 font-bold text-red-600">Delete</button></form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="py-6 text-zinc-500" colspan="5">No listings yet. Add your first spare part.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-5 shadow-sm">
                    <h2 class="mb-4 text-xl font-black">Inquiry inbox</h2>
                    <div class="grid gap-3">
                        @forelse($inquiries as $inquiry)
                            <div class="rounded border border-zinc-200 p-4">
                                <div class="flex flex-col justify-between gap-2 sm:flex-row">
                                    <b>{{ $inquiry->customer_name }}</b>
                                    <span class="text-sm font-bold text-red-600">{{ $inquiry->customer_phone }}</span>
                                </div>
                                <p class="mt-1 text-sm text-zinc-600">Part: <b>{{ $inquiry->product?->title }}</b></p>
                                <p class="mt-2 text-sm text-zinc-700">{{ $inquiry->message }}</p>
                            </div>
                        @empty
                            <p class="text-zinc-500">No inquiries yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
