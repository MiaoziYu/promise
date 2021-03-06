<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Promise by miaoziyu</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- API Token -->
    <script>
        @if (!empty($user->api_token))
            const API_TOKEN = "{{ $user->api_token }}";
        @endif
    </script>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    <!-- Scripts -->
    {{--<script defer src="https://use.fontawesome.com/releases/v5.0.1/js/all.js"></script>--}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

{{--@include('partials.nav')--}}

<main id="app">
    <top-nav></top-nav>
    <div class="container">
        @yield('content')
    </div>
</main>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.6.0/Sortable.min.js"></script>
<script type="text/javascript" src="{{ mix('/js/app.js') }}"></script>

</body>
</html>
