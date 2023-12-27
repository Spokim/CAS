<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div id="app">
        <div class="vh-100">
            <div class="container my-5">
                <h1 class="text-center">Conveniant adminstrative system</h1>
            </div>

            <main class="col-lg-10 d-flex flex-column mx-auto" style="max-height: 100vh; overflow-y: auto;">
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>
