<x-layouts.app title="Register">
    <section class="mx-auto max-w-2xl px-4 py-12">
        <form method="POST" action="{{ route('register.store') }}" class="rounded-lg bg-white p-6 shadow-sm">
            @csrf
            <h1 class="text-3xl font-black">Create an account</h1>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <label class="flex cursor-pointer items-center gap-3 rounded-md border border-zinc-300 p-4 font-bold">
                    <input type="radio" name="role" value="seller" @checked(old('role', 'seller') === 'seller')>
                    Seller account
                </label>
                <label class="flex cursor-pointer items-center gap-3 rounded-md border border-zinc-300 p-4 font-bold">
                    <input type="radio" name="role" value="garage" @checked(old('role') === 'garage')>
                    Garage account
                </label>
            </div>
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <input name="name" value="{{ old('name') }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Full name or dealer name" required>
                <input name="email" type="email" value="{{ old('email') }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Email address" required>
                <input name="phone" value="{{ old('phone') }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Phone / WhatsApp" required>
                <input name="location" value="{{ old('location') }}" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Location" required>
                <input name="password" type="password" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Password" required>
                <input name="password_confirmation" type="password" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Confirm password" required>
            </div>
            @if($errors->any())<p class="mt-4 text-sm font-bold text-red-600">{{ $errors->first() }}</p>@endif
            <button class="mt-6 w-full rounded-md bg-red-600 px-4 py-3 font-extrabold text-white">Register account</button>
        </form>
    </section>
</x-layouts.app>
