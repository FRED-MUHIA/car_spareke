<x-layouts.app title="Admin Dashboard">
    @php($homeContent = $siteSettings->homepageContent())
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-extrabold uppercase text-red-600">Platform control center</p>
                <h1 class="mt-1 text-4xl font-black">Admin dashboard</h1>
                <p class="mt-2 text-zinc-600">Manage users, shops, products, categories, garages, pricing plans, and seller verification.</p>
            </div>
            <a class="rounded-md bg-zinc-950 px-5 py-3 font-extrabold text-white" href="{{ route('home') }}">View Site</a>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($counts as $label => $count)
                <div class="rounded-lg bg-white p-5 shadow-sm">
                    <p class="text-sm font-bold uppercase text-zinc-500">{{ $label }}</p>
                    <b class="text-3xl">{{ $count }}</b>
                </div>
            @endforeach
        </div>

        <div class="mt-8 rounded-lg border {{ $siteSettings->maintenance_mode ? 'border-amber-300 bg-amber-50' : 'border-emerald-200 bg-white' }} p-5 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <p class="text-sm font-extrabold uppercase {{ $siteSettings->maintenance_mode ? 'text-amber-700' : 'text-emerald-700' }}">Website visibility</p>
                    <h2 class="mt-1 text-xl font-black">
                        Maintenance mode is {{ $siteSettings->maintenance_mode ? 'on' : 'off' }}
                    </h2>
                    <p class="mt-1 text-sm text-zinc-600">
                        {{ $siteSettings->maintenance_mode ? 'Visitors see a maintenance page. Logged-in admins can still browse and manage the site.' : 'Visitors can browse the website normally.' }}
                    </p>
                </div>
                <form method="POST" action="{{ route('admin.maintenance-mode.update') }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="maintenance_mode" value="{{ $siteSettings->maintenance_mode ? 0 : 1 }}">
                    <button class="rounded-md {{ $siteSettings->maintenance_mode ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-600 hover:bg-amber-700' }} px-5 py-3 text-sm font-extrabold text-white">
                        {{ $siteSettings->maintenance_mode ? 'Switch Off' : 'Switch On' }}
                    </button>
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.site-settings.update') }}" enctype="multipart/form-data" class="mt-8 rounded-lg bg-white p-5 shadow-sm" data-collapsible-panel>
            @csrf
            @method('PATCH')
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                <div>
                    <h2 class="text-xl font-black">Site and homepage content</h2>
                    <p class="mt-1 text-sm text-zinc-600">Update branding, footer copy, homepage headings, cards, and call-to-action text.</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="rounded-md border border-zinc-300 px-5 py-3 text-sm font-extrabold text-zinc-800" data-collapsible-toggle>Expand</button>
                    <button class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-extrabold text-white">Save Changes</button>
                </div>
            </div>

            @if($errors->any())
                <div class="mt-4 rounded-md border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="{{ $errors->any() ? '' : 'hidden' }}" data-collapsible-content>
                <div class="mt-5 grid gap-5 lg:grid-cols-2">
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Site name
                        <input name="site_name" value="{{ old('site_name', $siteSettings->site_name) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Footer heading
                        <input name="footer_heading" value="{{ old('footer_heading', $siteSettings->footer_heading) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>

                <label class="grid gap-2 text-sm font-bold text-zinc-700 lg:col-span-2">
                    Footer description
                    <textarea name="footer_description" rows="3" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">{{ old('footer_description', $siteSettings->footer_description) }}</textarea>
                </label>

                <label class="grid gap-2 text-sm font-bold text-zinc-700">
                    Logo
                    <div class="flex items-center gap-4 rounded-md border border-zinc-200 p-3">
                        @if($siteSettings->logo_url)
                            <img class="h-14 w-14 rounded-md object-contain" src="{{ $siteSettings->logo_url }}" alt="{{ $siteSettings->site_name }} logo">
                        @else
                            <span class="grid h-14 w-14 place-items-center rounded-md bg-red-600 font-black text-white">SP</span>
                        @endif
                        <input type="file" name="logo" accept=".png,.jpg,.jpeg,.webp,.svg" class="w-full text-sm font-medium">
                    </div>
                </label>

                <label class="grid gap-2 text-sm font-bold text-zinc-700">
                    Favicon
                    <div class="flex items-center gap-4 rounded-md border border-zinc-200 p-3">
                        @if($siteSettings->favicon_url)
                            <img class="h-10 w-10 rounded object-contain" src="{{ $siteSettings->favicon_url }}" alt="Current favicon">
                        @else
                            <span class="grid h-10 w-10 place-items-center rounded bg-zinc-950 text-xs font-black text-white">ICO</span>
                        @endif
                        <input type="file" name="favicon" accept=".ico,.png,.jpg,.jpeg,.webp,.svg" class="w-full text-sm font-medium">
                    </div>
                    </label>
                </div>

                <div class="mt-8 border-t border-zinc-200 pt-6">
                <h3 class="text-lg font-black">Homepage hero</h3>
                <div class="mt-4 grid gap-5 lg:grid-cols-2">
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Hero badge
                        <input name="home[hero_badge]" value="{{ old('home.hero_badge', $homeContent['hero_badge']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Hero title
                        <input name="home[hero_title]" value="{{ old('home.hero_title', $homeContent['hero_title']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700 lg:col-span-2">
                        Hero subtitle
                        <textarea name="home[hero_subtitle]" rows="2" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">{{ old('home.hero_subtitle', $homeContent['hero_subtitle']) }}</textarea>
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Part search label
                        <input name="home[part_prompt]" value="{{ old('home.part_prompt', $homeContent['part_prompt']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Part search placeholder
                        <input name="home[part_placeholder]" value="{{ old('home.part_placeholder', $homeContent['part_placeholder']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700 lg:col-span-2">
                        Search helper note
                        <input name="home[search_note]" value="{{ old('home.search_note', $homeContent['search_note']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700 lg:col-span-2">
                        Common issues
                        <input name="home[common_issues]" value="{{ old('home.common_issues', implode(', ', $homeContent['common_issues'])) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                </div>
                </div>

                <div class="mt-8 border-t border-zinc-200 pt-6">
                <h3 class="text-lg font-black">Trust section</h3>
                <div class="mt-4 grid gap-5 lg:grid-cols-2">
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Eyebrow
                        <input name="home[trust_eyebrow]" value="{{ old('home.trust_eyebrow', $homeContent['trust_eyebrow']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Title
                        <input name="home[trust_title]" value="{{ old('home.trust_title', $homeContent['trust_title']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    @foreach($homeContent['trust_cards'] as $index => $card)
                        <div class="rounded-md border border-zinc-200 p-4">
                            <h4 class="font-black">Card {{ $index + 1 }}</h4>
                            <label class="mt-3 grid gap-2 text-sm font-bold text-zinc-700">
                                Title
                                <input name="home[trust_cards][{{ $index }}][title]" value="{{ old("home.trust_cards.$index.title", $card['title']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                            </label>
                            <label class="mt-3 grid gap-2 text-sm font-bold text-zinc-700">
                                Description
                                <textarea name="home[trust_cards][{{ $index }}][description]" rows="3" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">{{ old("home.trust_cards.$index.description", $card['description']) }}</textarea>
                            </label>
                        </div>
                    @endforeach
                </div>
                </div>

                <div class="mt-8 border-t border-zinc-200 pt-6">
                <h3 class="text-lg font-black">Homepage sections</h3>
                <div class="mt-4 grid gap-5 lg:grid-cols-2">
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Category eyebrow
                        <input name="home[categories_eyebrow]" value="{{ old('home.categories_eyebrow', $homeContent['categories_eyebrow']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Category title
                        <input name="home[categories_title]" value="{{ old('home.categories_title', $homeContent['categories_title']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Trending eyebrow
                        <input name="home[trending_eyebrow]" value="{{ old('home.trending_eyebrow', $homeContent['trending_eyebrow']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Trending title
                        <input name="home[trending_title]" value="{{ old('home.trending_title', $homeContent['trending_title']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Shops title
                        <input name="home[shops_title]" value="{{ old('home.shops_title', $homeContent['shops_title']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        Garages title
                        <input name="home[garages_title]" value="{{ old('home.garages_title', $homeContent['garages_title']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        CTA title
                        <input name="home[cta_title]" value="{{ old('home.cta_title', $homeContent['cta_title']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700">
                        CTA button
                        <input name="home[cta_button]" value="{{ old('home.cta_button', $homeContent['cta_button']) }}" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-zinc-700 lg:col-span-2">
                        CTA text
                        <textarea name="home[cta_text]" rows="2" class="rounded-md border border-zinc-300 px-3 py-3 font-medium text-zinc-950">{{ old('home.cta_text', $homeContent['cta_text']) }}</textarea>
                    </label>
                </div>
                </div>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.category-icons.update') }}" enctype="multipart/form-data" class="mt-8 rounded-lg bg-white p-5 shadow-sm" data-collapsible-panel>
            @csrf
            @method('PATCH')
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                <div>
                    <h2 class="text-xl font-black">Category icons</h2>
                    <p class="mt-1 text-sm text-zinc-600">Add or change the icons shown on homepage category cards.</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="rounded-md border border-zinc-300 px-5 py-3 text-sm font-extrabold text-zinc-800" data-collapsible-toggle>Expand</button>
                    <button class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-extrabold text-white">Save Icons</button>
                </div>
            </div>

            <div class="hidden" data-collapsible-content>
                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($iconCategories as $category)
                        <label class="grid gap-2 rounded-md border border-zinc-200 p-4 text-sm font-bold text-zinc-700">
                            <span class="flex items-center gap-3">
                                <span class="grid h-10 w-10 place-items-center rounded-md bg-zinc-100 text-2xl">
                                    @if($category->icon_url)
                                        <img class="h-8 w-8 object-contain" src="{{ $category->icon_url }}" alt="{{ $category->name }} icon">
                                    @else
                                        {{ $category->icon ?: '•' }}
                                    @endif
                                </span>
                                <span>{{ $category->name }}</span>
                            </span>
                            <input name="categories[{{ $category->id }}][icon]" value="{{ old("categories.$category->id.icon", $category->icon) }}" class="rounded-md border border-zinc-300 px-3 py-3 text-xl font-medium text-zinc-950" placeholder="Emoji or text icon, e.g. ⚙️">
                            <input type="file" name="categories[{{ $category->id }}][icon_file]" accept=".png,.jpg,.jpeg,.webp,.svg" class="rounded-md border border-zinc-300 px-3 py-3 text-sm font-medium text-zinc-950">
                        </label>
                    @endforeach
                </div>
            </div>
        </form>

        <div class="mt-8 rounded-lg border border-red-100 bg-white p-5 shadow-sm">
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <h2 class="text-xl font-black">Pending seller verification</h2>
                    <p class="mt-1 text-sm text-zinc-600">New accounts need admin approval before login. Garage accounts only appear publicly after uploading a working licence.</p>
                </div>
                <span class="rounded-md bg-red-600 px-3 py-2 text-sm font-black text-white">{{ $pendingUsers->count() }} pending</span>
            </div>
            <div class="mt-5 grid gap-3">
                @forelse($pendingUsers as $user)
                    <div class="flex flex-col justify-between gap-3 rounded-md border border-zinc-200 p-4 sm:flex-row sm:items-center">
                        <div>
                            <b>{{ $user->name }}</b>
                            <p class="text-sm text-zinc-600">{{ $user->email }} · {{ $user->role }} · {{ $user->phone }} · {{ $user->location }}</p>
                            @if($user->role === 'garage')
                                @if($user->garage?->licenseUrl())
                                    <a class="mt-1 inline-flex text-sm font-bold text-red-600" href="{{ $user->garage->licenseUrl() }}" target="_blank" rel="noopener">View uploaded licence</a>
                                @else
                                    <p class="mt-1 text-sm font-bold text-amber-700">Licence not uploaded yet. You can still verify account access.</p>
                                @endif
                            @endif
                        </div>
                        <form method="POST" action="{{ route('admin.users.verify', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-md bg-red-600 px-4 py-2 text-sm font-extrabold text-white">Verify Account</button>
                        </form>
                    </div>
                @empty
                    <p class="rounded-md bg-zinc-100 p-4 text-zinc-500">No pending accounts.</p>
                @endforelse
            </div>
            @if($errors->has('verification'))
                <p class="mt-4 rounded-md bg-amber-50 p-3 text-sm font-bold text-amber-700">{{ $errors->first('verification') }}</p>
            @endif
        </div>

        <div class="mt-8 rounded-lg border border-blue-100 bg-white p-5 shadow-sm">
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <h2 class="text-xl font-black">Garage licence review</h2>
                    <p class="mt-1 text-sm text-zinc-600">Review uploaded working licences, then verify garages for public visibility.</p>
                </div>
                <span class="rounded-md bg-blue-600 px-3 py-2 text-sm font-black text-white">{{ $pendingGarageVerifications->count() }} to review</span>
            </div>
            <div class="mt-5 grid gap-3">
                @forelse($pendingGarageVerifications as $garage)
                    <div class="flex flex-col justify-between gap-3 rounded-md border border-zinc-200 p-4 sm:flex-row sm:items-center">
                        <div>
                            <b>{{ $garage->name }}</b>
                            <p class="text-sm text-zinc-600">{{ $garage->user?->email }} · {{ $garage->location }}</p>
                            <a class="mt-1 inline-flex text-sm font-bold text-red-600" href="{{ $garage->licenseUrl() }}" target="_blank" rel="noopener">View uploaded licence</a>
                        </div>
                        <form method="POST" action="{{ route('admin.garages.public-verify', $garage) }}">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-extrabold text-white">Verify Public Listing</button>
                        </form>
                    </div>
                @empty
                    <p class="rounded-md bg-zinc-100 p-4 text-zinc-500">No garage licences waiting for review.</p>
                @endforelse
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-2">
            <div class="rounded-lg bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-xl font-black">Product management</h2>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[680px] text-left text-sm">
                        <thead><tr class="border-b text-zinc-500"><th class="py-3">Product</th><th>Seller</th><th>Status</th><th>Price</th><th>Action</th></tr></thead>
                        <tbody>
                            @foreach($latestProducts as $product)
                                <tr class="border-b">
                                    <td class="py-3 font-bold">{{ $product->title }}<br><span class="font-normal text-zinc-500">{{ $product->category->name }}</span></td>
                                    <td>{{ $product->shop?->name ?? $product->seller_name }}</td>
                                    <td><span class="rounded {{ $product->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} px-2 py-1 font-bold">{{ $product->status }}</span></td>
                                    <td>KES {{ number_format($product->price) }}</td>
                                    <td>
                                        @if(in_array($product->status, ['active', 'inactive'], true))
                                            <form method="POST" action="{{ route('admin.products.status', $product) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $product->status === 'active' ? 'inactive' : 'active' }}">
                                                <button class="rounded-md {{ $product->status === 'active' ? 'bg-red-600' : 'bg-emerald-600' }} px-3 py-2 text-xs font-extrabold text-white">
                                                    {{ $product->status === 'active' ? 'Make Inactive' : 'Make Active' }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs font-bold text-zinc-500">No action</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-lg bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-xl font-black">Latest inquiries</h2>
                <div class="grid gap-3">
                    @foreach($latestInquiries as $inquiry)
                        <div class="rounded-md border border-zinc-200 p-4">
                            <b>{{ $inquiry->customer_name }}</b>
                            <p class="mt-1 text-sm text-zinc-600">{{ $inquiry->product?->title }} · {{ $inquiry->customer_phone }} · {{ $inquiry->status }}</p>
                        </div>
                    @endforeach
                    @if($latestInquiries->isEmpty())
                        <p class="text-zinc-500">No inquiries yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            <div class="rounded-lg bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-xl font-black">Shops</h2>
                <div class="grid gap-3">
                    @foreach($shops as $shop)
                        <div class="rounded-md border border-zinc-200 p-4">
                            <b>{{ $shop->name }}</b>
                            <p class="text-sm text-zinc-600">{{ $shop->location }} · {{ $shop->products_count }} listings · {{ $shop->status }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-lg bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-xl font-black">Garages</h2>
                <div class="grid gap-3">
                    @foreach($garages as $garage)
                        <div class="rounded-md border border-zinc-200 p-4">
                            <b>{{ $garage->name }}</b>
                            <p class="text-sm text-zinc-600">{{ $garage->location }} · rating {{ $garage->rating }} · {{ $garage->review_count }} reviews</p>
                            @if($garage->public_verified_at)
                                <p class="mt-1 text-sm font-bold text-blue-600">Public verified {{ $garage->public_verified_at->diffForHumans() }}</p>
                                <form method="POST" action="{{ route('admin.garages.public-revoke', $garage) }}" class="mt-3 flex flex-col gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input name="license_rejection_reason" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" placeholder="Reason e.g. licence not genuine" required>
                                    <button class="w-fit rounded-md border border-red-300 px-3 py-2 text-xs font-extrabold text-red-600">Revoke Public Verification</button>
                                </form>
                            @elseif($garage->license_rejection_reason)
                                <p class="mt-1 text-sm font-bold text-red-600">Revoked: {{ $garage->license_rejection_reason }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-lg bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-xl font-black">Pricing plans</h2>
                <div class="grid gap-3">
                    @foreach($plans as $plan)
                        <div class="rounded-md border border-zinc-200 p-4">
                            <b>{{ $plan->name }}</b>
                            <p class="text-sm text-zinc-600">KES {{ number_format($plan->price) }}/{{ $plan->billing_period }} · {{ $plan->listing_limit ?? 'Unlimited' }} listings</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-8 rounded-lg bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-xl font-black">Recently verified users</h2>
            <div class="grid gap-3 md:grid-cols-2">
                @foreach($verifiedUsers as $user)
                    <div class="rounded-md border border-zinc-200 p-4">
                        <div class="flex flex-col justify-between gap-3 lg:flex-row lg:items-start">
                            <div>
                                <b>{{ $user->name }}</b>
                                <p class="text-sm text-zinc-600">{{ $user->email }} · {{ $user->role }} · verified {{ $user->email_verified_at?->diffForHumans() }}</p>
                                @if($user->isOnProbation())
                                    <p class="mt-1 text-sm font-bold text-amber-700">Under probation: {{ $user->probation_reason }}</p>
                                @endif
                            </div>
                            @if($user->id !== Auth::id())
                                <div class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('admin.users.probation', $user) }}" class="flex gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="probation" value="{{ $user->isOnProbation() ? 0 : 1 }}">
                                        @unless($user->isOnProbation())
                                            <input name="probation_reason" class="w-36 rounded-md border border-zinc-300 px-2 py-1 text-xs" placeholder="Reason">
                                        @endunless
                                        <button class="rounded-md {{ $user->isOnProbation() ? 'bg-emerald-600' : 'bg-amber-600' }} px-3 py-2 text-xs font-extrabold text-white">
                                            {{ $user->isOnProbation() ? 'Remove Probation' : 'Probation' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.delete', $user) }}" onsubmit="return confirm('Delete this account? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-red-300 px-3 py-2 text-xs font-extrabold text-red-600">Delete</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
