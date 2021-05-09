<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Мой НМК | {{$title}}</title>
        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset("assets/img/favicon/apple-touch-icon.png")}}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{asset("assets/img/favicon/favicon-32x32.png")}}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset("assets/img/favicon/favicon-16x16.png")}}">
        <link rel="manifest" href="{{asset("assets/img/favicon/site.webmanifest")}}">
        <link rel="mask-icon" href="{{asset("assets/img/favicon/safari-pinned-tab.svg")}}" color="#5bbad5">
        <meta name="apple-mobile-web-app-title" content="Мой НМК">
        <meta name="application-name" content="Мой НМК">
        <meta name="msapplication-TileColor" content="#2196f3">
        <meta name="theme-color" content="#2196f3">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
        <!-- Extra details for Live View on GitHub Pages -->

        <!-- Icons -->
        <link rel="stylesheet" href="{{asset("assets/img/icons/mdi/css/materialdesignicons.min.css")}}">
        <link href="{{ asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
        <!-- Argon CSS -->
        <link type="text/css" href="{{ asset('assets/css/argon.css') }}" rel="stylesheet">
        @stack('styles')
    </head>
    <body class="{{ $class ?? '' }}">

        @auth()
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endauth
        @if(!\Illuminate\Support\Facades\Request::exists('mobile'))
            @if(isset($tab))
                @include('layouts.navbars.sidebar', ['active_tab' => $tab])
            @else
                @include('layouts.navbars.sidebar', ['active_tab' => ''])
            @endif
        @endif

        <div class="main-content">
            @if(!\Illuminate\Support\Facades\Request::input('mobile'))
                @include('layouts.navbars.navbar')
            @endif
            @yield('content')
            @if(!\Illuminate\Support\Facades\Request::input('mobile'))
                @include("layouts.footers.default")
            @endif
        </div>

        <script src="{{ asset('assets/vendor/jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/js-cookie/js.cookie.js') }}"></script>
        <script src="{{ asset('assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>

        @stack('js')

        <!-- Argon JS -->
        <script src="{{ asset('assets/js/argon.js') }}"></script>
    </body>
</html>
