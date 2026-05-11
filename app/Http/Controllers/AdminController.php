<?php

namespace App\Http\Controllers;

use App\Mail\SellerApprovedMail;
use App\Models\Category;
use App\Models\Garage;
use App\Models\Inquiry;
use App\Models\PricingPlan;
use App\Models\Product;
use App\Models\Shop;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->guest(URL::route('login'));
        }

        abort_unless(Auth::user()?->role === 'admin', 403);

        return view('admin.dashboard', [
            'counts' => [
                'users' => User::count(),
                'shops' => Shop::count(),
                'products' => Product::count(),
                'categories' => Category::count(),
                'garages' => Garage::count(),
                'inquiries' => Inquiry::count(),
                'plans' => PricingPlan::count(),
            ],
            'latestProducts' => Product::with(['category', 'shop'])->latest()->take(6)->get(),
            'latestInquiries' => Inquiry::with('product')->latest()->take(6)->get(),
            'pendingUsers' => User::with('garage')->whereNull('email_verified_at')->latest()->get(),
            'pendingGarageVerifications' => Garage::with('user')
                ->whereNotNull('user_id')
                ->whereNotNull('license_path')
                ->whereNull('public_verified_at')
                ->whereHas('user', fn ($query) => $query->whereNotNull('email_verified_at'))
                ->latest()
                ->get(),
            'verifiedUsers' => User::whereNotNull('email_verified_at')->latest()->take(8)->get(),
            'shops' => Shop::withCount('products')->latest()->take(8)->get(),
            'garages' => Garage::latest()->take(8)->get(),
            'plans' => PricingPlan::latest()->get(),
            'siteSettings' => SiteSetting::current(),
            'iconCategories' => Category::orderBy('name')->get(),
        ]);
    }

    public function updateSiteSettings(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'admin', 403);

        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:80'],
            'footer_heading' => ['required', 'string', 'max:120'],
            'footer_description' => ['nullable', 'string', 'max:600'],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:ico,png,jpg,jpeg,webp,svg', 'max:1024'],
            'home.hero_badge' => ['required', 'string', 'max:100'],
            'home.hero_title' => ['required', 'string', 'max:160'],
            'home.hero_subtitle' => ['required', 'string', 'max:260'],
            'home.part_prompt' => ['required', 'string', 'max:120'],
            'home.part_placeholder' => ['required', 'string', 'max:160'],
            'home.search_note' => ['required', 'string', 'max:220'],
            'home.common_issues' => ['nullable', 'string', 'max:300'],
            'home.trust_eyebrow' => ['required', 'string', 'max:100'],
            'home.trust_title' => ['required', 'string', 'max:140'],
            'home.trust_cards' => ['required', 'array', 'size:4'],
            'home.trust_cards.*.title' => ['required', 'string', 'max:80'],
            'home.trust_cards.*.description' => ['required', 'string', 'max:220'],
            'home.categories_eyebrow' => ['required', 'string', 'max:100'],
            'home.categories_title' => ['required', 'string', 'max:120'],
            'home.trending_eyebrow' => ['required', 'string', 'max:100'],
            'home.trending_title' => ['required', 'string', 'max:120'],
            'home.shops_title' => ['required', 'string', 'max:100'],
            'home.garages_title' => ['required', 'string', 'max:100'],
            'home.cta_title' => ['required', 'string', 'max:120'],
            'home.cta_text' => ['required', 'string', 'max:220'],
            'home.cta_button' => ['required', 'string', 'max:60'],
        ]);

        $settings = SiteSetting::current();
        $homeContent = $data['home'];
        $homeContent['common_issues'] = collect(explode(',', $homeContent['common_issues'] ?? ''))
            ->map(fn ($issue) => trim($issue))
            ->filter()
            ->values()
            ->all();

        $settings->fill([
            'site_name' => $data['site_name'],
            'footer_heading' => $data['footer_heading'],
            'footer_description' => $data['footer_description'] ?? null,
            'home_content' => $homeContent,
        ]);

        if ($request->hasFile('logo')) {
            $this->replaceStoredFile($settings, 'logo_path', $request->file('logo')->store('site', 'public'));
        }

        if ($request->hasFile('favicon')) {
            $this->replaceStoredFile($settings, 'favicon_path', $request->file('favicon')->store('site', 'public'));
        }

        $settings->save();

        return back()->with('status', 'Header, footer, logo, and favicon settings updated.');
    }

    public function toggleMaintenanceMode(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'admin', 403);

        $data = $request->validate([
            'maintenance_mode' => ['required', 'boolean'],
        ]);

        $settings = SiteSetting::current();
        $settings->update([
            'maintenance_mode' => $data['maintenance_mode'],
        ]);

        return back()->with('status', $settings->maintenance_mode
            ? 'Maintenance mode is now on. Only logged-in admins can view the website.'
            : 'Maintenance mode is now off. The website is visible to visitors.');
    }

    public function updateCategoryIcons(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'admin', 403);

        $data = $request->validate([
            'categories' => ['required', 'array'],
            'categories.*.icon' => ['nullable', 'string', 'max:40'],
            'categories.*.icon_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:1024'],
        ]);

        foreach ($data['categories'] as $categoryId => $values) {
            $category = Category::find($categoryId);

            if (! $category) {
                continue;
            }

            $category->fill([
                'icon' => $values['icon'] ?? null,
            ]);

            if ($request->hasFile("categories.$categoryId.icon_file")) {
                if ($category->icon_path) {
                    Storage::disk('public')->delete($category->icon_path);
                }

                $category->icon_path = $request->file("categories.$categoryId.icon_file")->store('category-icons', 'public');
            }

            $category->save();
        }

        return back()->with('status', 'Category icons updated.');
    }

    public function verifyUser(User $user): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'admin', 403);

        $user->update([
            'email_verified_at' => now(),
            'probation_at' => null,
            'probation_reason' => null,
        ]);

        Mail::to($user->email)->send(new SellerApprovedMail($user));

        return back()->with('status', "{$user->name} has been verified and can now log in.");
    }

    public function updateProductStatus(Request $request, Product $product): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'admin', 403);

        $data = $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $product->update([
            'status' => $data['status'],
            'sold_at' => $data['status'] === 'active' ? null : $product->sold_at,
        ]);

        return back()->with('status', "{$product->title} is now {$product->status}.");
    }

    public function verifyGaragePublicListing(Garage $garage): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'admin', 403);
        abort_unless($garage->user?->email_verified_at, 422, 'Verify the garage account first.');
        abort_unless($garage->license_path, 422, 'A working licence must be uploaded first.');

        $garage->update([
            'public_verified_at' => now(),
            'license_rejection_reason' => null,
        ]);

        return back()->with('status', "{$garage->name} is now verified and visible publicly.");
    }

    public function revokeGaragePublicListing(Request $request, Garage $garage): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'admin', 403);

        $data = $request->validate([
            'license_rejection_reason' => ['required', 'string', 'max:255'],
        ]);

        $garage->update([
            'public_verified_at' => null,
            'license_rejection_reason' => $data['license_rejection_reason'],
        ]);

        return back()->with('status', "{$garage->name}'s public verification has been revoked.");
    }

    public function toggleUserProbation(Request $request, User $user): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'admin', 403);
        abort_if($user->id === Auth::id(), 422, 'You cannot put your own admin account on probation.');

        $data = $request->validate([
            'probation' => ['required', 'boolean'],
            'probation_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($data['probation']
            ? [
                'probation_at' => now(),
                'probation_reason' => $data['probation_reason'] ?: 'Account placed under admin review.',
            ]
            : [
                'probation_at' => null,
                'probation_reason' => null,
            ]);

        return back()->with('status', $user->isOnProbation()
            ? "{$user->name} is now under probation and cannot log in."
            : "{$user->name} has been removed from probation.");
    }

    public function deleteUser(User $user): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'admin', 403);
        abort_if($user->id === Auth::id(), 422, 'You cannot delete your own admin account.');

        $name = $user->name;
        $user->delete();

        return back()->with('status', "{$name}'s account has been deleted.");
    }

    private function replaceStoredFile(SiteSetting $settings, string $field, string $path): void
    {
        if ($settings->{$field}) {
            Storage::disk('public')->delete($settings->{$field});
        }

        $settings->{$field} = $path;
    }
}
