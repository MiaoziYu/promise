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
</head>
<body>

{{--@include('partials.nav')--}}

<main id="app">
    @yield('content')
</main>

<!-- Scripts -->
<script type="text/javascript" src="/js/app.js"></script>

</body>
</html>
