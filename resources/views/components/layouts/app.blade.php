<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php($siteSettings = \App\Models\SiteSetting::current())
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? $siteSettings->site_name }}</title>
    <link rel="icon" href="{{ $siteSettings->favicon_url ?? asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800" rel="stylesheet">
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-zinc-100 font-sans text-zinc-950 antialiased">
    <header class="sticky top-0 z-40 border-b border-zinc-800 bg-zinc-950 text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                @if($siteSettings->logo_url)
                    <img class="h-14 max-h-14 w-auto max-w-[220px] object-contain" src="{{ $siteSettings->logo_url }}" alt="{{ $siteSettings->site_name }} logo">
                @else
                    <span class="grid h-10 w-10 place-items-center rounded-md bg-red-600 font-black">SP</span>
                @endif
                <span class="text-base font-extrabold sm:text-lg">{{ $siteSettings->site_name }}</span>
            </a>
            <nav class="hidden items-center gap-6 text-sm font-semibold lg:flex">
                <a class="hover:text-yellow-300" href="{{ route('home') }}">Home</a>
                <a class="hover:text-yellow-300" href="{{ route('parts.index') }}">Browse Parts</a>
                <a class="hover:text-yellow-300" href="{{ route('shops.index') }}">Shops</a>
                <a class="hover:text-yellow-300" href="{{ route('shops.index') }}">Find Shops</a>
                <a class="hover:text-yellow-300" href="{{ route('garages.index') }}">Find Garages</a>
                <a class="hover:text-yellow-300" href="{{ route('pricing') }}">Pricing</a>
            </nav>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden rounded-md border border-zinc-700 px-3 py-2 text-sm font-bold sm:inline-flex">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="hidden rounded-md px-3 py-2 text-sm font-bold text-zinc-300 sm:inline-flex">Logout</button></form>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-md px-3 py-2 text-sm font-bold text-zinc-300 sm:inline-flex">Login</a>
                @endauth
                <a href="{{ auth()->check() ? route('sell') : route('register') }}" class="rounded-md bg-red-600 px-4 py-2 text-sm font-extrabold text-white shadow-sm hover:bg-red-700">Sell Parts</a>
            </div>
        </div>
    </header>

    @if (session('status'))
        <div class="border-b border-yellow-200 bg-yellow-100 px-4 py-3 text-center text-sm font-semibold text-zinc-900">{{ session('status') }}</div>
    @endif

    <main>
        {{ $slot }}
    </main>

    <footer class="border-t border-zinc-800 bg-zinc-950 text-zinc-300">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-4 lg:px-8">
            <div class="md:col-span-2">
                <h2 class="text-xl font-extrabold text-white">{{ $siteSettings->footer_heading }}</h2>
                <p class="mt-3 max-w-xl text-sm leading-6">{{ $siteSettings->footer_description }}</p>
            </div>
            <div>
                <h3 class="font-bold text-white">Marketplace</h3>
                <div class="mt-3 grid gap-2 text-sm">
                    <a href="{{ route('parts.index') }}">Browse Parts</a>
                    <a href="{{ route('shops.index') }}">Find Shops</a>
                    <a href="{{ route('garages.index') }}">Find Garages</a>
                </div>
            </div>
            <div>
                <h3 class="font-bold text-white">Sellers</h3>
                <div class="mt-3 grid gap-2 text-sm">
                    <a href="{{ route('sell') }}">Publish Listing</a>
                    <a href="{{ route('pricing') }}">Pricing Plans</a>
                    <a href="{{ route('register') }}">Create Account</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
