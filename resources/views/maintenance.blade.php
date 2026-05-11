<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance | {{ $siteSettings->site_name }}</title>
    <link rel="icon" href="{{ $siteSettings->favicon_url ?? asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800" rel="stylesheet">
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-white font-sans text-[#060b3d] antialiased">
    <main class="min-h-screen overflow-hidden px-5 py-10 sm:px-8 lg:px-12">
        <section class="mx-auto grid min-h-[calc(100vh-5rem)] max-w-7xl items-center gap-12 lg:grid-cols-[1fr_0.95fr]">
            <div>
                <div class="mb-10 flex items-center gap-3">
                    @if($siteSettings->logo_url)
                        <img class="h-14 max-w-[220px] object-contain" src="{{ $siteSettings->logo_url }}" alt="{{ $siteSettings->site_name }} logo">
                    @else
                        <span class="grid h-12 w-12 place-items-center rounded-md bg-[#060b3d] text-sm font-black text-white">SP</span>
                    @endif
                    <span class="text-lg font-extrabold">{{ $siteSettings->site_name }}</span>
                </div>

                <h1 class="max-w-3xl text-5xl font-black leading-tight sm:text-6xl lg:text-7xl">We are tidying up!</h1>
                <p class="mt-3 text-2xl font-semibold">Sorry for the inconvenience!</p>
                <p class="mt-6 max-w-xl text-lg font-medium leading-8">
                    Currently updating servers to improve service.<br>
                    Thank you for your patience!
                </p>

                <p class="mt-12 text-lg font-semibold">
                    Your beloved site will be available in:
                    <span class="ml-2 font-black tracking-wider">01:02:41</span>
                </p>

            </div>

            <div class="relative min-h-[420px] lg:min-h-[620px]" aria-hidden="true">
                <div class="absolute left-[7%] top-[8%] h-[82%] w-[86%] rounded-full bg-zinc-100"></div>
                <div class="absolute left-[16%] top-[18%] h-[68%] w-[16%] skew-x-[-10deg] rounded-t-full bg-zinc-200"></div>
                <div class="absolute left-[30%] top-[4%] h-[82%] w-[18%] skew-x-[-10deg] rounded-t-full bg-zinc-200"></div>
                <div class="absolute right-[2%] top-[22%] h-[58%] w-[14%] skew-x-[8deg] rounded-t-full bg-zinc-200"></div>

                <div class="absolute right-[19%] top-[3%] h-[82%] w-[30%] rounded-lg bg-zinc-700 shadow-xl">
                    @foreach(range(1, 6) as $row)
                        <div class="mx-auto mt-5 h-[10%] w-[78%] rounded-md border-2 border-zinc-300 px-3 py-2">
                            <div class="flex h-full items-end gap-1">
                                @foreach([25, 45, 30, 60, 78, 38, 70, 52, 88, 40] as $bar)
                                    <span class="w-2 rounded-sm bg-zinc-200" style="height: {{ $bar }}%"></span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="absolute right-[13%] top-[15%] h-14 w-14 rounded-full border-8 border-zinc-300"></div>
                <div class="absolute right-[2%] top-[19%] h-20 w-20 rounded-full border-[12px] border-zinc-300"></div>

                <div class="absolute right-[16%] top-[13%] h-[42%] w-[20%]">
                    <div class="mx-auto h-10 w-10 rounded-full bg-zinc-950"></div>
                    <div class="mx-auto -mt-2 h-12 w-8 rounded-full bg-white"></div>
                    <div class="mx-auto h-28 w-24 rounded-t-full bg-red-600"></div>
                    <div class="absolute left-0 top-28 h-24 w-16 rotate-[-18deg] rounded-md bg-zinc-800"></div>
                    <div class="absolute bottom-0 left-10 h-28 w-4 rotate-[8deg] bg-zinc-800"></div>
                    <div class="absolute bottom-0 right-8 h-28 w-4 rotate-[-5deg] bg-zinc-800"></div>
                </div>

                <div class="absolute right-[1%] bottom-[5%] h-[34%] w-[24%] border-l-4 border-r-4 border-zinc-400">
                    <div class="absolute left-0 top-1/4 h-1 w-full bg-zinc-400"></div>
                    <div class="absolute left-0 top-1/2 h-1 w-full bg-zinc-400"></div>
                    <div class="absolute left-0 top-3/4 h-1 w-full bg-zinc-400"></div>
                </div>

                <div class="absolute left-[12%] bottom-[12%] h-24 w-[46%] border-b-4 border-t-4 border-zinc-400"></div>
                <div class="absolute left-[18%] bottom-[2%] h-36 w-4 rotate-[20deg] bg-zinc-400"></div>
                <div class="absolute left-[45%] bottom-[2%] h-36 w-4 rotate-[-20deg] bg-zinc-400"></div>

                <div class="absolute left-[14%] bottom-[18%] h-[28%] w-[22%]">
                    <div class="h-16 w-24 rounded-full bg-zinc-950"></div>
                    <div class="ml-12 -mt-8 h-10 w-10 rounded-full bg-white"></div>
                    <div class="mt-0 h-24 w-28 rounded-t-full bg-red-600"></div>
                    <div class="absolute bottom-0 left-16 h-28 w-5 rotate-[-24deg] bg-white"></div>
                    <div class="absolute bottom-1 right-4 h-28 w-5 rotate-[24deg] bg-white"></div>
                </div>

                <div class="absolute left-[30%] bottom-[24%] h-20 w-32 rounded-md bg-zinc-800 shadow-lg">
                    <div class="mx-auto mt-7 h-4 w-4 rounded-full bg-zinc-600"></div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
