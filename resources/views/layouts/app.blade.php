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
    <link rel="stylesheet" type="text/css" href="{{ asset('custom/plugins/datatables/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('custom/css/webkit-custom.css')  }}">
</head>
<body class="hold-transition light-skin theme-primary fixed">

<div class="wrapper">

    <!-- Page Header Start-->
    <header class="main-header">
        @include('layouts.navigation')
    </header>
    <!-- Page Header Ends-->

    <aside class="main-sidebar">
        @include('layouts.sidebar')
    </aside>
    <!-- Page Sidebar End-->

    <div class="content-wrapper">
        <div class="container-full">

            @if(isset($header))
                <div class="content-header pt-10">
                    <div class="d-flex j-flex align-items-center">
                        <div class="mr-auto">
                            <h3 class="page-title">{{ $header }}</h3>
                            {{ $bread }}
                        </div>
                        @if(isset($breadRight))
                            {{ $breadRight }}
                        @endif
                    </div>
                </div>
            @endif

            <section class="content">
                @if($errors->any())

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                {{$errors->first()}}
                            </div>
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </section>
        </div>
    </div>

    <footer class="main-footer">
        <p class="pull-left">Copyright {{ date('Y') }} © All rights reserved</p>
        <p class="pull-right"></p>
    </footer>

</div>
<!-- ./wrapper -->

<!-- Page Content overlay -->
<div class="w3-overlay w3-animate-opacity" onclick="w3_close()" style="display:none; cursor:pointer"
     id="myOverlay"></div>
<!-- ./side demo panel -->

<div class="modal fade" id="footerMultiPopupModal" tabindex="-1"
     aria-labelledby="alarmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>

<!-- Vendor JS -->
<script src="{{ asset('themes/webkit/webkit-main/js/vendors.min.js') }}"></script>
<script src="{{ asset('themes/webkit/assets/icons/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('themes/webkit/assets/vendor_components/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('themes/webkit/assets/vendor_components/morris.js/morris.min.js') }}"></script>
<script src="{{ asset('themes/webkit/assets/vendor_components/apexcharts-bundle/dist/apexcharts.js') }}"></script>
<script src="{{ asset('themes/webkit/assets/vendor_components/echarts/dist/echarts-en.min.js') }}"></script>
{{--<script src="{{ asset('themes/webkit/assets/vendor_components/datatable/datatables.min.js') }}"></script>--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script
    src="{{ asset('themes/webkit/assets/vendor_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<script src="{{ asset('custom/plugins/chartjs/Chart.min.js') }}"></script>
<script src="{{ asset('custom/plugins/chartjs/chartjs-adapter-date-fns.bundle.min.js') }}"></script>
<script src="{{ asset('custom/plugins/datatables/datatables.min.js') }}"></script>
<!-- WebkitX Admin App -->
<script src="{{ asset('themes/webkit/webkit-main/js/template.js') }}"></script>

@yield('page-scripts')

<script>
    $.fn.htmlTo = function(elem) {
        return this.each(function() {
            $(elem).html($(this).html());
        });
    }

    var tooltip_init = {
        init: function () {
            $("button").tooltip();
            $("a").tooltip();
            $("input").tooltip();
            $("img").tooltip();
            $("div").tooltip();
            $("p").tooltip();
            $("h1").tooltip();
            $("h2").tooltip();
            $("h3").tooltip();
            $("h4").tooltip();
            $("h5").tooltip();
            $("h6").tooltip();
            $("li").tooltip();
            $("span").tooltip();
        }
    };
    tooltip_init.init();
</script>

</body>
</html>
