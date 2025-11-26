<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>Edesip</title>
        <meta
        content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
        name="viewport"
        />
        <link
        rel="icon"
        href="{{ asset('img/logo.png') }}"
        type="image/x-icon"
        />

        <!-- Fonts and icons -->
        <script src="{{ asset('admin/js/plugin/webfont/webfont.min.js') }}"></script>
        <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
            families: [
                "Font Awesome 5 Solid",
                "Font Awesome 5 Regular",
                "Font Awesome 5 Brands",
                "simple-line-icons",
            ],
            urls: ["{{ asset('admin/css/fonts.min.css') }}"],
            },
            active: function () {
            sessionStorage.fonts = true;
            },
        });
        </script>

        <!-- CSS Files -->
        <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('admin/css/plugins.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('admin/css/kaiadmin.min.css') }}" />

        <!-- CSS Just for demo purpose, don't include it in your project -->
        <link rel="stylesheet" href="{{ asset('admin//css/demo.css') }}" />
    </head>
    <body>
        <div class="wrapper">
        <!-- Sidebar -->
        @include('layouts.partial.sidebar')
        <!-- End Sidebar -->
            <div class="main-panel">
                <div class="main-header">
                    @include('layouts.partial.header')
                </div>
                <div class="container">
                    <div class="page-inner">
                        @yield('content')
                    </div>
            </div>
            <footer class="footer">
                @include('layouts.partial.footer')
            </footer>
        </div>
        <!--   Core JS Files   -->
        <script src="{{ asset('admin/js/core/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('admin/js/core/popper.min.js') }}"></script>
        <script src="{{ asset('admin/js/core/bootstrap.min.js') }}"></script>

        <!-- jQuery Scrollbar -->
        <!-- jQuery Scrollbar -->
        <script src="{{ asset('admin/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

        <!-- Chart JS -->
        <script src="{{ asset('admin/js/plugin/chart.js/chart.min.js') }}"></script>

        <!-- jQuery Sparkline -->
        <script src="{{ asset('admin/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

        <!-- Chart Circle -->
        <script src="{{ asset('admin/js/plugin/chart-circle/circles.min.js') }}"></script>

        <!-- Datatables -->
        <script src="{{ asset('admin/js/plugin/datatables/datatables.min.js') }}"></script>

        <!-- Bootstrap Notify -->
        <script src="{{ asset('admin/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

        <!-- jQuery Vector Maps -->
        <script src="{{ asset('admin/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>

        <!-- Kaiadmin principal -->
        <script src="{{ asset('admin/js/kaiadmin.min.js') }}"></script>

        <!-- Demo y configuraciones (opcional, puedes eliminarlos para aligerar la app) -->
        <!-- <script src="{{ asset('admin/assets/js/setting-demo.js') }}"></script>
        <script src="{{ asset('admin/assets/js/demo.js') }}"></script> -->

            $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
                type: "line",
                height: "70",
                width: "100%",
                lineWidth: "2",
                lineColor: "#177dff",
                fillColor: "rgba(23, 125, 255, 0.14)",
            });

            $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
                type: "line",
                height: "70",
                width: "100%",
                lineWidth: "2",
                lineColor: "#f3545d",
                fillColor: "rgba(243, 84, 93, .14)",
            });

            $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
                type: "line",
                height: "70",
                width: "100%",
                lineWidth: "2",
                lineColor: "#ffa534",
                fillColor: "rgba(255, 165, 52, .14)",
            });
        </script>
        @stack('scripts')
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
        </form>
    </body>
</html>
