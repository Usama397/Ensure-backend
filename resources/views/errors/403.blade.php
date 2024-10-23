<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="{{ asset('themes/endless/assets/images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('themes/endless/assets/images/favicon.png') }}" type="image/x-icon">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/webkit/webkit-main/css/vendors_css.css')  }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/webkit/webkit-main/css/style.css')  }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/webkit/webkit-main/css/skin_color.css')  }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('custom/css/webkit-custom.css')  }}">
</head>

<body class="hold-transition theme-primary bg-img" style="background-image: url({{ asset('themes/webkit/images/auth-bg/bg-4.jpg') }})">

<section class="error-page h-p100">
    <div class="container h-p100">
        <div class="row h-p100 align-items-center justify-content-center text-center">
            <div class="col-lg-7 col-md-10 col-12">
                <div class="rounded30 p-50">
                    <img src="{{ asset('themes/webkit/images/auth-bg/404.jpg')  }}" class="max-w-200" alt="" />
                    <h1>Page Not Found !</h1>
                    <h3>looks like, page doesn't exist</h3>
                    <div class="my-30"><a href="{{ route("dashboard") }}" class="btn btn-danger">Back to dashboard</a></div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vendor JS -->
<script src="{{ asset('themes/webkit/webkit-main/js/vendors.min.js') }}"></script>
<script src="{{ asset('themes/webkit/assets/icons/feather-icons/feather.min.js') }}"></script>

@yield('page-scripts')

</body>
</html>
