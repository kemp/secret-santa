<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Secret Santa</title>

        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>
    <body>
        <div class="w-full max-w-md mx-auto mt-2">
            <h1 class="text-center text-lg text-gray-600">Secret Santa App</h1>

            <div class="mt-5 max-w-full">
                @yield('main')
            </div>
        </div>

        @stack('scripts')
    </body>
</html>