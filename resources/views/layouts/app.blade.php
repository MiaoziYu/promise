<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- API Token -->
    <script>
        @if (!empty($user->api_token))
            const API_TOKEN = "{{ $user->api_token }}";
        @endif
    </script>

    <!-- CSS -->
    <link rel="stylesheet" href="/css/app.css">

    <!-- Scripts -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.1/js/all.js"></script>
</head>
<body>

{{--@include('partials.nav')--}}

<main id="app" class="container">
    @yield('content')
</main>

<!-- Scripts -->
<script type="text/javascript" src="/js/app.js"></script>

</body>
</html>
