<?php

namespace App\Http\Controllers;

use App\Models\CarMake;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\PlanPayment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerDashboardController extends Controller
{
    private const FREE_ACTIVE_LISTING_LIMIT = 5;

    public function index(): View
    {
        $user = Auth::user();

        abort_unless($user->role === 'seller', 403);

        $activeCount = $user->products()->where('status', 'active')->count();
        $activePlanPayment = $this->activePlanPayment($user);
        $freeListingLimit = $this->listingLimit($user, $activePlanPayment);

        return view('seller.dashboard', [
            'shop' => $user->shop,
            'selectedPlan' => $user->pricingPlan,
            'latestPlanPayment' => $user->pricing_plan_id
                ? $user->planPayments()->where('pricing_plan_id', $user->pricing_plan_id)->latest()->first()
                : null,
            'products' => $user->products()->with(['category', 'make', 'model'])->latest()->get(),
            'inquiries' => $user->inquiries()->with('product')->latest()->take(8)->get(),
            'activeCount' => $activeCount,
            'soldCount' => $user->products()->where('status', 'sold')->count(),
            'totalCount' => $user->products()->count(),
            'inquiryCount' => $user->inquiries()->count(),
            'freeListingLimit' => $freeListingLimit,
            'freeListingLimitReached' => $freeListingLimit !== null && $activeCount >= $freeListingLimit,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        if ($redirect = $this->sellerListingRedirect()) {
            return $redirect;
        }

        if ($this->freeListingLimitReached()) {
            return $this->upgradeRedirect();
        }

        return view('seller.sell', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        if ($redirect = $this->sellerListingRedirect()) {
            return $redirect;
        }

        if ($this->freeListingLimitReached()) {
            return $this->upgradeRedirect();
        }

        $data = $this->validatedProduct($request);
        unset($data['images'], $data['image_url']);

        $product = Product::create($data + [
            'user_id' => Auth::id(),
            'shop_id' => Auth::user()?->shop?->id,
            'seller_name' => Auth::user()?->name ?? $request->seller_name,
            'seller_phone' => Auth::user()?->phone ?? $request->seller_phone,
            'seller_whatsapp' => $request->seller_whatsapp ?? Auth::user()?->phone,
            'status' => 'active',
            'slug' => $this->uniqueSlug($request->title),
            'images' => $this->imagePayload($request),
        ]);

        return redirect()->route('parts.show', $product)->with('status', 'Your spare part listing is live.');
    }

    public function edit(Product $product): View|RedirectResponse
    {
        if ($redirect = $this->sellerListingRedirect()) {
            return $redirect;
        }

        abort_unless($product->user_id === Auth::id(), 403);

        return view('seller.sell', $this->formData() + ['product' => $product]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        if ($redirect = $this->sellerListingRedirect()) {
            return $redirect;
        }

        abort_unless($product->user_id === Auth::id(), 403);

        $data = $this->validatedProduct($request);
        unset($data['images'], $data['image_url']);

        $product->update($data + [
            'images' => $this->imagePayload($request, $product),
        ]);

        return redirect()->route('seller.dashboard')->with('status', 'Listing updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($redirect = $this->sellerListingRedirect()) {
            return $redirect;
        }

        abort_unless($product->user_id === Auth::id(), 403);
        $product->delete();

        return back()->with('status', 'Listing deleted.');
    }

    public function markSold(Product $product): RedirectResponse
    {
        if ($redirect = $this->sellerListingRedirect()) {
            return $redirect;
        }

        abort_unless($product->user_id === Auth::id(), 403);
        $product->update(['status' => 'sold', 'sold_at' => now()]);

        return back()->with('status', 'Listing marked as sold.');
    }

    public function storeShop(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo_path' => ['nullable', 'string', 'max:1000'],
            'banner_path' => ['nullable', 'string', 'max:1000'],
            'logo_image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'banner_image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ]);

        $data['logo_path'] = $this->uploadedShopImagePath($request, 'logo_image', Auth::user()?->shop?->logo_path)
            ?? $data['logo_path']
            ?? Auth::user()?->shop?->logo_path;
        $data['banner_path'] = $this->uploadedShopImagePath($request, 'banner_image', Auth::user()?->shop?->banner_path)
            ?? $data['banner_path']
            ?? Auth::user()?->shop?->banner_path;
        unset($data['logo_image'], $data['banner_image']);

        Shop::updateOrCreate(
            ['user_id' => Auth::id()],
            $data + ['slug' => Str::slug($data['name']).'-'.Auth::id(), 'status' => 'active']
        );

        return back()->with('status', 'Shop profile saved.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'makes' => CarMake::with('models')->orderBy('name')->get(),
            'models' => CarModel::orderBy('name')->get(),
        ];
    }

    private function validatedProduct(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'car_make_id' => ['nullable', 'exists:car_makes,id'],
            'car_model_id' => ['nullable', 'exists:car_models,id'],
            'part_type' => ['required', 'string', 'max:255'],
            'part_number' => ['nullable', 'string', 'max:120'],
            'year_from' => ['required', 'integer', 'min:1980', 'max:2035'],
            'year_to' => ['required', 'integer', 'min:1980', 'max:2035', 'gte:year_from'],
            'condition' => ['required', 'in:New,Used,Refurbished'],
            'price' => ['required', 'numeric', 'min:0'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:3000'],
            'seller_name' => ['nullable', 'string', 'max:255'],
            'seller_phone' => ['nullable', 'string', 'max:40'],
            'seller_whatsapp' => ['nullable', 'string', 'max:40'],
            'image_url' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array', 'max:2'],
            'images.*' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ]);
    }

    private function imagePayload(Request $request, ?Product $product = null): array
    {
        $existingImages = collect($product?->images ?? [])
            ->flatten()
            ->filter(fn ($image) => is_string($image) && trim($image) !== '')
            ->values()
            ->all();

        $manualImages = $request->filled('image_url') ? [$request->image_url] : [];
        $existingAlternatives = $manualImages === []
            ? $existingImages
            : array_values(array_filter($existingImages, fn ($image) => $image !== ($existingImages[0] ?? null)));
        $storedImages = [];

        if ($request->hasFile('images')) {
            $storedImages = collect($request->file('images'))
                ->flatten()
                ->filter(fn ($image) => $image instanceof UploadedFile && $image->isValid())
                ->map(fn ($image) => '/storage/'.$image->store('parts', 'public'))
                ->values()
                ->all();
        }

        $images = collect([...$manualImages, ...$existingAlternatives, ...$storedImages])
            ->unique()
            ->take(2)
            ->values()
            ->all();

        return $images ?: ['https://images.unsplash.com/photo-1607860108855-64acf2078ed9?auto=format&fit=crop&w=900&q=80'];
    }

    private function uploadedShopImagePath(Request $request, string $field, ?string $currentPath): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        if ($currentPath && str_starts_with($currentPath, '/storage/')) {
            Storage::disk('public')->delete(substr($currentPath, 9));
        }

        return '/storage/'.$request->file($field)->store('shops', 'public');
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $count = 2;

        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$count}";
            $count++;
        }

        return $slug;
    }

    private function sellerListingRedirect(): ?RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()
                ->route('register')
                ->with('status', 'Create a seller account first. After admin verification, you can list and sell spare parts.');
        }

        abort_unless(Auth::user()->role === 'seller' && Auth::user()->email_verified_at, 403);

        return null;
    }

    private function freeListingLimitReached(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        $limit = $this->listingLimit($user, $this->activePlanPayment($user));

        return $limit !== null && $user->products()->where('status', 'active')->count() >= $limit;
    }

    private function upgradeRedirect(): RedirectResponse
    {
        return redirect()
            ->route('pricing')
            ->with('status', 'Your free listing limit is complete. Upgrade your package to add more spare parts.');
    }

    private function activePlanPayment(User $user): ?PlanPayment
    {
        if (! $user->pricing_plan_id) {
            return null;
        }

        return $user->planPayments()
            ->with('plan')
            ->where('pricing_plan_id', $user->pricing_plan_id)
            ->where('status', 'paid')
            ->latest()
            ->first();
    }

    private function listingLimit(User $user, ?PlanPayment $activePlanPayment): ?int
    {
        if (! $activePlanPayment) {
            return self::FREE_ACTIVE_LISTING_LIMIT;
        }

        return $activePlanPayment->plan?->listing_limit;
    }
}
