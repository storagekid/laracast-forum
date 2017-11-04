<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <script>
        
        window.App = {!! json_encode([

            'csrf-token' => csrf_token(),
            'user' => Auth::user(),
            'signedIn' => Auth::check(),

        ]) !!};

    </script>
    <style type="text/css">
        
        .level {

            display: flex;
            align-items: center;

        }

        .flex {

            flex: 1;

        }

        [v-cloak] {

            display: none;
        }

    </style>
</head>
<body>
    <div id="app">
        @include('nav.main')

        @yield('content')

        
        <flash message="{{ session('flash') }}"></flash>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
