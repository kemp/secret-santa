<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        {{-- Private links live in URLs, so never leak them to other sites or search engines --}}
        <meta name="referrer" content="same-origin">
        <meta name="robots" content="noindex, nofollow">
        <meta name="theme-color" content="#fbf8f3" media="(prefers-color-scheme: light)">
        <meta name="theme-color" content="#0c1f19" media="(prefers-color-scheme: dark)">

        <title>@yield('title', 'Secret Santa')</title>

        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎁</text></svg>">

        @fonts
        @vite(['resources/css/app.css', 'resources/ts/app.ts'])
    </head>
    <body>
        <div class="snow-light" aria-hidden="true"></div>
        <div class="snow" aria-hidden="true"></div>

        <div class="mx-auto w-full @yield('width', 'max-w-xl') px-4 pt-6 pb-16 sm:pt-10">
            <header class="mb-6 flex items-center justify-center gap-2 sm:mb-8">
                <span class="text-2xl" aria-hidden="true">🎁</span>
                <p class="font-display text-xl font-semibold text-pine-800 dark:text-pine-100">Secret Santa</p>
            </header>

            <main class="space-y-6">
                @include('flash')

                @yield('main')
            </main>

            <footer class="mt-12 text-center text-xs text-stone-400 dark:text-stone-500">
                Made with care for the family &middot; {{ now()->year }}
            </footer>
        </div>

        <div id="toast" role="status" aria-live="polite"
             class="pointer-events-none fixed inset-x-0 bottom-6 z-50 mx-auto w-fit translate-y-4 rounded-full bg-stone-900 px-4 py-2 text-sm font-medium text-white opacity-0 shadow-lg transition duration-300 dark:bg-white dark:text-stone-900"></div>

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
