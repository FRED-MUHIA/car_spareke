<x-layouts.app title="Sell Parts">
    @php
        $editing = isset($product);
        $currentImageUrl = $editing
            ? collect($product->images ?? [])->flatten()->filter(fn ($image) => is_string($image) && trim($image) !== '')->first()
            : null;
    @endphp
    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-black">{{ $editing ? 'Edit spare part listing' : 'Sell spare parts' }}</h1>
        <form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('seller.products.update', $product) : route('seller.products.store') }}" class="mt-8 rounded-lg bg-white p-6 shadow-sm">
            @csrf
            @if($editing) @method('PUT') @endif
            <div class="grid gap-5 md:grid-cols-2">
                <label class="grid gap-2 text-sm font-bold text-zinc-700">
                    Main product image
                    <input name="images[]" type="file" accept="image/*,.webp" capture="environment" class="rounded-md border border-zinc-300 px-3 py-3 text-base font-normal text-zinc-950">
                </label>
                <label class="grid gap-2 text-sm font-bold text-zinc-700">
                    Alternative product image
                    <input name="images[]" type="file" accept="image/*,.webp" capture="environment" class="rounded-md border border-zinc-300 px-3 py-3 text-base font-normal text-zinc-950">
                </label>
                <input name="image_url" value="{{ old('image_url', $currentImageUrl ?? '') }}" class="rounded-md border border-zinc-300 px-3 py-3 md:col-span-2" placeholder="Or paste a photo URL">
                <input name="title" value="{{ old('title', $product->title ?? '') }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Product title" required>
                <select name="category_id" class="rounded-md border border-zinc-300 px-3 py-3" required><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '')==$category->id)>{{ $category->name }}</option>@endforeach</select>
                <select name="car_make_id" data-car-make-select class="rounded-md border border-zinc-300 px-3 py-3"><option value="">Car make</option>@foreach($makes as $make)<option value="{{ $make->id }}" @selected(old('car_make_id', $product->car_make_id ?? '')==$make->id)>{{ $make->name }}</option>@endforeach</select>
                <select name="car_model_id" data-car-model-select class="rounded-md border border-zinc-300 px-3 py-3"><option value="">Car model</option>@foreach($models as $model)<option value="{{ $model->id }}" data-make="{{ $model->car_make_id }}" @selected(old('car_model_id', $product->car_model_id ?? '')==$model->id)>{{ $model->name }}</option>@endforeach</select>
                <input name="part_type" value="{{ old('part_type', $product->part_type ?? '') }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Part type" required>
                <input name="part_number" value="{{ old('part_number', $product->part_number ?? '') }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Part number / OEM number">
                <select name="condition" class="rounded-md border border-zinc-300 px-3 py-3" required>@foreach(['New','Used','Refurbished'] as $condition)<option @selected(old('condition', $product->condition ?? '')===$condition)>{{ $condition }}</option>@endforeach</select>
                <select id="compatible_year" name="year_from" class="rounded-md border border-zinc-300 px-3 py-3" required>
                    <option value="">Compatible car year</option>
                    @for($year = now()->year + 1; $year >= 1980; $year--)
                        <option value="{{ $year }}" @selected(old('year_from', $product->year_from ?? '') == $year)>{{ $year }}</option>
                    @endfor
                </select>
                <input id="year_to" type="hidden" name="year_to" value="{{ old('year_to', $product->year_to ?? $product->year_from ?? '') }}">
                <input name="price" value="{{ old('price', $product->price ?? '') }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Price KES" required>
                <input name="location" value="{{ old('location', $product->location ?? Auth::user()?->location) }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Location" required>
                @guest
                    <input name="seller_name" value="{{ old('seller_name') }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Seller name" required>
                    <input name="seller_phone" value="{{ old('seller_phone') }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Seller phone" required>
                @endguest
                <input name="seller_whatsapp" value="{{ old('seller_whatsapp', $product->seller_whatsapp ?? Auth::user()?->phone) }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="+254 WhatsApp number">
                <textarea name="description" rows="5" class="rounded-md border border-zinc-300 px-3 py-3 md:col-span-2" placeholder="Description" required>{{ old('description', $product->description ?? '') }}</textarea>
            </div>
            @if($errors->any())<p class="mt-4 text-sm font-bold text-red-600">{{ $errors->first() }}</p>@endif
            <button class="mt-6 rounded-md bg-red-600 px-6 py-3 font-extrabold text-white">{{ $editing ? 'Update listing' : 'Publish listing' }}</button>
        </form>
    </section>
    <script>
        document.getElementById('compatible_year')?.addEventListener('change', function () {
            document.getElementById('year_to').value = this.value;
        });
    </script>
</x-layouts.app>
