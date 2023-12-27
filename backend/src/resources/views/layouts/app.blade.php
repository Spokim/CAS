<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/jquery.js'])
</head>

<body>
    <div id="app">
        <div class="container-fluid min-vh-100 bg-dark-subtle">
            <div class="row min-vh-100">
                <nav class="navbar navbar-expand-lg d-lg-none sidebar">
                    <x-navbar.mobileNav />
                </nav>
                <nav class="sidebar d-none d-lg-flex flex-column col-2">
                    <x-navbar.desktopNav />
                </nav>
                <main class="col-lg-10 d-flex flex-column desktop-main">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
</body>

</html>
