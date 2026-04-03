<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-layout="vertical"
    data-sidebar="dark"
    data-sidebar-size="lg"
    data-topbar="light"
    data-layout-width="fluid"
    data-layout-position="fixed"
    data-layout-style="default"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="/vendor/velzon/assets/images/logo-sm.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="/vendor/velzon/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/vendor/velzon/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="/vendor/velzon/assets/css/app.min.css" rel="stylesheet" type="text/css" />
        <link href="/vendor/velzon/assets/css/custom.min.css" rel="stylesheet" type="text/css" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
