<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        {{-- Private links live in URLs, so never leak them to other sites or search engines --}}
        <meta name="referrer" content="same-origin">
        <meta name="robots" content="noindex, nofollow">
        <meta name="theme-color" content="#eaf1ed" media="(prefers-color-scheme: light)">
        <meta name="theme-color" content="#0c1f19" media="(prefers-color-scheme: dark)">

        <title>@yield('title', 'Secret Santa')</title>

        <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23c6343b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='8' width='18' height='4' rx='1'/%3E%3Cpath d='M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7M12 8v13M12 8S10.5 3.5 8 3.5a2.25 2.25 0 0 0 0 4.5M12 8s1.5-4.5 4-4.5a2.25 2.25 0 0 1 0 4.5'/%3E%3C/svg%3E">

        @fonts
        @vite(['resources/css/app.css', 'resources/ts/app.ts'])
    </head>
    <body>
        <div class="snow" aria-hidden="true"></div>

        <div class="mx-auto w-full @yield('width', 'max-w-xl') px-4 pt-6 pb-16 sm:pt-10">
            <header class="mb-8 flex items-center justify-center gap-2 text-berry-600 dark:text-berry-300">
                @include('icon', ['name' => 'gift', 'class' => 'size-6'])
                <p class="font-display text-xl text-pine-800 dark:text-pine-100">Secret Santa</p>
            </header>

            <main class="space-y-5">
                @include('flash')

                @yield('main')
            </main>

            <footer class="mt-14 text-center text-sm text-stone-500 dark:text-stone-400">
                Made with care for the family &middot; {{ now()->year }}
            </footer>
        </div>

        <div id="toast" role="status" aria-live="polite"
             class="pointer-events-none fixed inset-x-0 bottom-6 z-50 mx-auto w-fit translate-y-4 rounded-full bg-stone-900 px-4 py-2 text-sm font-bold text-white opacity-0 shadow-lg transition duration-300 dark:bg-white dark:text-stone-900"></div>

        @stack('scripts')

        @if(session()->has('clear-saved-input'))
            <script>
                ((window) => {
                    setTimeout(() => {
                        window.clearSavedInput('add-participant')
                    }, 0);
                })(window);
            </script>
        @endif
    </body>
</html>
