<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'Secret Santa')</title>

        @vite(['resources/css/app.css', 'resources/ts/app.ts'])
    </head>
    <body>
        <div class="w-full max-w-md mx-auto mt-2 px-4">
            <h1 class="text-center text-lg text-gray-600">Secret Santa</h1>

            <div class="mt-5 max-w-full pb-10">
                @yield('main')
            </div>
        </div>

        @stack('scripts')

        @if(session()->has('clear-saved-input'))
            <script>
                ((window) => {
                    setTimeout(() => {
                        console.log('Clearing saved input');

                        window.clearSavedInput('add-participant')
                    }, 0);
                })(window);
            </script>
        @endif
    </body>
</html>
