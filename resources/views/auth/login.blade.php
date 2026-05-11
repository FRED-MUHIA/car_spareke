<x-layouts.app title="Login">
    <section class="mx-auto max-w-md px-4 py-12">
        <form method="POST" action="{{ route('login.store') }}" class="rounded-lg bg-white p-6 shadow-sm">
            @csrf
            <h1 class="text-3xl font-black">Seller login</h1>
            @error('email')<p class="mt-3 text-sm font-bold text-red-600">{{ $message }}</p>@enderror
            <div class="mt-6 grid gap-4">
                <input name="email" type="email" value="{{ old('email') }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Email address" required>
                <input name="password" type="password" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Password" required>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember"> Remember me</label>
                <button class="rounded-md bg-red-600 px-4 py-3 font-extrabold text-white">Login</button>
                <a class="text-center text-sm font-bold text-red-600" href="{{ route('register') }}">Create a seller or garage account</a>
            </div>
        </form>
    </section>
</x-layouts.app>
