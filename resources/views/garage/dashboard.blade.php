<x-layouts.app title="Garage Dashboard">
    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-extrabold uppercase text-red-600">Garage account</p>
                <div class="mt-1 flex flex-wrap items-center gap-3">
                    <h1 class="text-4xl font-black">Garage dashboard</h1>
                    @if($garage?->isVerified())
                        <span class="rounded bg-blue-600 px-2 py-1 text-xs font-black uppercase text-white">Verified</span>
                    @else
                        <span class="rounded bg-zinc-500 px-2 py-1 text-xs font-black uppercase text-white">Not verified</span>
                    @endif
                </div>
                <p class="mt-2 text-zinc-600">Create or update your garage profile so buyers can find your services.</p>
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
                @unless($garage?->license_path)
                    <p class="mt-3 rounded-md bg-amber-50 p-3 text-sm font-bold text-amber-700">Upload your working licence before your garage becomes visible to the public.</p>
                @endunless
                @if($garage?->license_rejection_reason)
                    <p class="mt-3 rounded-md bg-red-50 p-3 text-sm font-bold text-red-700">Licence review issue: {{ $garage->license_rejection_reason }}. Upload a genuine licence for another review.</p>
                @endif
            </div>
            @if($garage)
                <a class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-extrabold text-white" href="{{ route('garages.show', $garage) }}">View Profile</a>
            @endif
        </div>

        <form method="POST" action="{{ route('garage.profile.update') }}" enctype="multipart/form-data" class="mt-8 rounded-lg bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            <div class="grid gap-5 md:grid-cols-2">
                <input name="name" value="{{ old('name', $garage->name ?? Auth::user()->name) }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Garage name" required>
                <input name="location" value="{{ old('location', $garage->location ?? Auth::user()->location) }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Location" required>
                <input name="phone" value="{{ old('phone', $garage->phone ?? Auth::user()->phone) }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Phone" required>
                <input name="whatsapp" value="{{ old('whatsapp', $garage->whatsapp ?? Auth::user()->phone) }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="+254 WhatsApp number">
                <label class="grid gap-2 text-sm font-bold text-zinc-700 md:col-span-2">
                    Garage image
                    <div class="grid gap-4 rounded-md border border-zinc-200 p-4 md:grid-cols-[220px_1fr] md:items-center">
                        <img class="aspect-[16/10] w-full rounded-md object-cover" src="{{ $garage?->image() ?? 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?auto=format&fit=crop&w=1200&q=80' }}" alt="{{ $garage->name ?? 'Garage image preview' }}">
                        <div class="grid gap-3">
                            <input type="file" name="image" accept="image/png,image/jpeg,image/webp" class="rounded-md border border-zinc-300 px-3 py-3 text-base font-normal text-zinc-950">
                            <input name="image_url" value="{{ old('image_url', $garage->image_url ?? '') }}" class="rounded-md border border-zinc-300 px-3 py-3 font-normal text-zinc-950" placeholder="Or paste a garage image URL">
                        </div>
                    </div>
                </label>
                <label class="grid gap-2 text-sm font-bold text-zinc-700 md:col-span-2">
                    Working licence
                    <div class="grid gap-3 rounded-md border border-zinc-200 p-4">
                        @if($garage?->licenseUrl())
                            <a class="text-sm font-extrabold text-red-600" href="{{ $garage->licenseUrl() }}" target="_blank" rel="noopener">View current uploaded licence</a>
                        @else
                            <p class="text-sm font-semibold text-amber-700">No licence uploaded yet. Your garage will not be public until this is uploaded and admin verifies your account.</p>
                        @endif
                        <input type="file" name="license" accept=".pdf,image/png,image/jpeg,image/webp" class="rounded-md border border-zinc-300 px-3 py-3 text-base font-normal text-zinc-950">
                    </div>
                </label>
                <div class="grid gap-3 md:col-span-2" data-service-list>
                    <div class="flex items-center justify-between gap-3">
                        <label class="text-sm font-bold text-zinc-700">Services</label>
                        <button type="button" class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-extrabold text-zinc-800" data-service-add>Add Service</button>
                    </div>
                    <div class="grid gap-3" data-service-items>
                        @foreach(old('services', $garage->services ?? ['']) as $service)
                            <div class="flex gap-2" data-service-item>
                                <input name="services[]" value="{{ $service }}" class="min-w-0 flex-1 rounded-md border border-zinc-300 px-3 py-3" placeholder="Service e.g. AC Repair">
                                <button type="button" class="rounded-md border border-red-200 px-3 py-2 text-sm font-extrabold text-red-600" data-service-remove>Remove</button>
                            </div>
                        @endforeach
                    </div>
                </div>
                <input name="specialization_brands" value="{{ old('specialization_brands', implode(', ', $garage->specialization_brands ?? [])) }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Car brands e.g. Toyota, BMW or leave blank for all">
                <textarea name="description" rows="5" class="rounded-md border border-zinc-300 px-3 py-3 md:col-span-2" placeholder="Garage description" required>{{ old('description', $garage->description ?? '') }}</textarea>
            </div>
            @if($errors->any())<p class="mt-4 text-sm font-bold text-red-600">{{ $errors->first() }}</p>@endif
            <button class="mt-6 rounded-md bg-red-600 px-6 py-3 font-extrabold text-white">Save Garage Profile</button>
        </form>
    </section>
    <script>
        document.querySelectorAll('[data-service-list]').forEach((list) => {
            const items = list.querySelector('[data-service-items]');
            const add = list.querySelector('[data-service-add]');

            const bindRemove = (button) => {
                button.addEventListener('click', () => {
                    if (items.querySelectorAll('[data-service-item]').length === 1) {
                        button.closest('[data-service-item]').querySelector('input').value = '';
                        return;
                    }

                    button.closest('[data-service-item]').remove();
                });
            };

            list.querySelectorAll('[data-service-remove]').forEach(bindRemove);

            add?.addEventListener('click', () => {
                const row = document.createElement('div');
                row.className = 'flex gap-2';
                row.dataset.serviceItem = '';
                row.innerHTML = '<input name="services[]" class="min-w-0 flex-1 rounded-md border border-zinc-300 px-3 py-3" placeholder="Service e.g. AC Repair"><button type="button" class="rounded-md border border-red-200 px-3 py-2 text-sm font-extrabold text-red-600" data-service-remove>Remove</button>';
                items.appendChild(row);
                bindRemove(row.querySelector('[data-service-remove]'));
                row.querySelector('input').focus();
            });
        });
    </script>
</x-layouts.app>
