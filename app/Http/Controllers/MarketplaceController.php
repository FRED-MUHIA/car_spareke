<?php

namespace App\Http\Controllers;

use App\Mail\SellerInquiryMail;
use App\Models\CarMake;
use App\Models\Category;
use App\Models\Garage;
use App\Models\GarageReview;
use App\Models\Inquiry;
use App\Models\PricingPlan;
use App\Models\Product;
use App\Models\Shop;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function home(): View
    {
        $siteSettings = SiteSetting::current();

        return view('marketplace.home', [
            'categories' => Category::withCount('products')->where('is_featured', true)->get(),
            'featuredProducts' => Product::with(['category', 'make', 'model', 'shop'])->where('status', 'active')->where('is_featured', true)->latest()->take(8)->get(),
            'featuredShops' => Shop::withCount('products')->where('is_featured', true)->take(4)->get(),
            'featuredGarages' => Garage::with('user')->publiclyVisible()->where('is_featured', true)->take(4)->get(),
            'makes' => CarMake::with('models')->orderBy('name')->get(),
            'homeContent' => $siteSettings->homepageContent(),
        ]);
    }

    public function browse(Request $request): View
    {
        $year = $request->integer('year') ?: null;

        $products = Product::query()
            ->with(['category', 'make', 'model', 'shop'])
            ->where('status', 'active')
            ->when($request->filled('q'), fn ($query) => $query->where(fn ($inner) => $inner
                ->where('title', 'like', "%{$request->q}%")
                ->orWhere('part_type', 'like', "%{$request->q}%")
                ->orWhere('part_number', 'like', "%{$request->q}%")
                ->orWhere('description', 'like', "%{$request->q}%")
                ->orWhereHas('make', fn ($make) => $make->where('name', 'like', "%{$request->q}%"))
                ->orWhereHas('model', fn ($model) => $model->where('name', 'like', "%{$request->q}%"))))
            ->when($request->filled('part_number'), fn ($query) => $query->where('part_number', 'like', "%{$request->part_number}%"))
            ->when($request->filled('category'), fn ($query) => $query->whereHas('category', fn ($category) => $category->where('slug', $request->category)))
            ->when($request->filled('make'), fn ($query) => $query->whereHas('make', fn ($make) => $make->where('slug', $request->make)))
            ->when($request->filled('model'), fn ($query) => $query->whereHas('model', fn ($model) => $model->where('slug', $request->model)))
            ->when($year, fn ($query) => $query->where('year_from', '<=', $year)->where('year_to', '>=', $year))
            ->when($request->filled('condition'), fn ($query) => $query->where('condition', $request->condition))
            ->when($request->filled('location'), fn ($query) => $query->where('location', 'like', "%{$request->location}%"))
            ->when($request->filled('min_price'), fn ($query) => $query->where('price', '>=', $request->integer('min_price')))
            ->when($request->filled('max_price'), fn ($query) => $query->where('price', '<=', $request->integer('max_price')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('marketplace.browse', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'makes' => CarMake::with('models')->orderBy('name')->get(),
        ]);
    }

    public function show(Product $product): View
    {
        abort_if(
            $product->status !== 'active'
            && Auth::user()?->role !== 'admin'
            && Auth::id() !== $product->user_id,
            404
        );

        $product->load(['category', 'make', 'model', 'shop']);

        return view('marketplace.product', [
            'product' => $product,
            'similar' => Product::with(['category', 'make', 'model', 'shop'])
                ->where('id', '!=', $product->id)
                ->where('category_id', $product->category_id)
                ->where('status', 'active')
                ->latest()
                ->take(12)
                ->get(),
        ]);
    }

    public function inquiry(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $inquiry = Inquiry::create($data + [
            'product_id' => $product->id,
            'shop_id' => $product->shop_id,
            'seller_id' => $product->user_id,
        ]);

        $product->loadMissing(['shop', 'user']);
        $sellerEmail = $product->user?->email ?? $product->shop?->email;

        if ($sellerEmail) {
            Mail::to($sellerEmail)->send(new SellerInquiryMail($inquiry->load('product')));
        }

        return back()->with('status', 'Inquiry sent. The seller has been emailed and can now contact you.');
    }

    public function shops(): View
    {
        return view('marketplace.shops', [
            'shops' => Shop::withCount('products')->where('status', 'active')->latest()->get(),
        ]);
    }

    public function garages(): View
    {
        return view('marketplace.garages', [
            'garages' => Garage::with(['user', 'reviews' => fn ($query) => $query->latest()])->publiclyVisible()->latest()->get(),
        ]);
    }

    public function garageShow(Garage $garage): View
    {
        $garage->load(['user', 'reviews' => fn ($query) => $query->latest()]);

        abort_if(
            ! $garage->isPubliclyVisible()
            && Auth::user()?->role !== 'admin'
            && Auth::id() !== $garage->user_id,
            404
        );

        return view('marketplace.garage', [
            'garage' => $garage,
        ]);
    }

    public function garageReview(Request $request, Garage $garage): RedirectResponse
    {
        $data = $request->validate([
            'reviewer_name' => ['required', 'string', 'max:80'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        GarageReview::create($data + ['garage_id' => $garage->id]);

        $garage->update([
            'rating' => round($garage->reviews()->avg('rating'), 1),
            'review_count' => $garage->reviews()->count(),
        ]);

        return back()->with('status', 'Review posted. Thank you for sharing your experience.');
    }

    public function pricing(): View
    {
        return view('marketplace.pricing', [
            'plans' => PricingPlan::orderBy('price')->get(),
        ]);
    }
}
