<?php

namespace App\Http\Controllers;

use App\Models\Garage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GarageDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        abort_unless($user?->role === 'garage', 403);

        return view('garage.dashboard', [
            'garage' => $user->garage()->first(),
            'selectedPlan' => $user->pricingPlan,
            'latestPlanPayment' => $user->pricing_plan_id
                ? $user->planPayments()->where('pricing_plan_id', $user->pricing_plan_id)->latest()->first()
                : null,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($user?->role === 'garage', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'image_url' => ['nullable', 'url', 'max:1000'],
            'image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'license' => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg,webp', 'max:4096'],
            'services' => ['nullable', 'array'],
            'services.*' => ['nullable', 'string', 'max:120'],
            'specialization_brands' => ['nullable', 'string', 'max:500'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        $garage = $user->garage()->first();
        $services = $this->cleanList($data['services'] ?? []);
        $specializationBrands = $this->csvToArray($data['specialization_brands'] ?? '');
        $imageUrl = $data['image_url'] ?? $garage?->image_url;

        if ($request->hasFile('image')) {
            if ($garage?->image_url && str_starts_with($garage->image_url, '/storage/')) {
                Storage::disk('public')->delete(substr($garage->image_url, 9));
            }

            $imageUrl = '/storage/'.$request->file('image')->store('garages', 'public');
        }

        $licensePath = $garage?->license_path;

        if ($request->hasFile('license')) {
            if ($licensePath) {
                Storage::disk('public')->delete($licensePath);
            }

            $licensePath = $request->file('license')->store('garage-licenses', 'public');
        }

        Garage::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $data['name'],
                'slug' => $this->uniqueSlug($data['name'], $garage?->id),
                'location' => $data['location'],
                'phone' => $data['phone'],
                'whatsapp' => $data['whatsapp'] ?? null,
                'image_url' => $imageUrl,
                'license_path' => $licensePath,
                'public_verified_at' => $request->hasFile('license') ? null : $garage?->public_verified_at,
                'license_rejection_reason' => $request->hasFile('license') ? null : $garage?->license_rejection_reason,
                'services' => $services,
                'specialization_brands' => $specializationBrands,
                'description' => $data['description'],
                'is_featured' => false,
            ]
        );

        return back()->with('status', 'Garage profile saved.');
    }

    private function csvToArray(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    private function cleanList(array $values): array
    {
        return collect($values)
            ->map(fn ($item) => is_string($item) ? trim($item) : '')
            ->filter()
            ->values()
            ->all();
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $count = 2;

        while (Garage::where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$count}";
            $count++;
        }

        return $slug;
    }
}
