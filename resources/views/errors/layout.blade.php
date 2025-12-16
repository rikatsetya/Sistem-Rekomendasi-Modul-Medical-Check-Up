<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="layout-wide customizer-hide" dir="ltr" data-skin="default"
    data-assets-path="{{ asset('vuexy/assets/') }}/" data-template="horizontal-menu-template" data-bs-theme="dark">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Medical Check Up | PJT 1</title>
    <meta name="description" content="@yield('description', 'Error Page')" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('vuexy/assets/img/favicon/icon.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/css/pages/page-misc.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('vuexy/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('vuexy/assets/js/config.js') }}"></script>
</head>

<body>
    <div class="container-xxl container-p-y">
        @yield('content')
    </div>

    <div class="container-fluid misc-bg-wrapper">
        <img src="{{ asset('vuexy/assets/img/illustrations/bg-shape-image-light.png') }}" height="355"
            alt="background" data-app-light-img="illustrations/bg-shape-image-light.png"
            data-app-dark-img="illustrations/bg-shape-image-dark.png" />
    </div>

    <!-- Core JS -->
    <script src="{{ asset('vuexy/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('vuexy/assets/js/main.js') }}"></script>
</body>

</html>
