<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('custom/img/favicon-32x32.png') }}" type="image/x-icon">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/webkit/webkit-main/css/vendors_css.css')  }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/webkit/webkit-main/css/style.css')  }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/webkit/webkit-main/css/skin_color.css')  }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('custom/css/webkit-custom.css')  }}">
</head>

<body class="jerry-login-mobile hold-transition theme-primary bg-img"
      style="background-image: url({{ asset('custom/img/login-bg.png') }})">

<div class="container h-p100">
    {{ $slot }}
</div>

<!-- Vendor JS -->
<script src="{{ asset('themes/webkit/webkit-main/js/vendors.min.js') }}"></script>
<script src="{{ asset('themes/webkit/assets/icons/feather-icons/feather.min.js') }}"></script>

@yield('page-scripts')

</body>
</html>
