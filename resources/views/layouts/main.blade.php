<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }}</title>
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="This is an example dashboard created using build-in elements and components.">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- CSS -->
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- ICONS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/font/bootstrap-icons.min.css">
    {{-- Jquery UI --}}
    <link rel="stylesheet" href="{{ asset('css/jqueryUI.css') }}">

    {{-- Chart JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>

    {{-- Toastr --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />


</head>

<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        <!-- Header -->
        <div class="app-header header-shadow">
            <div class="app-header__logo">
                <div class="logo-src fw-bolder fs-4">R-POS</div>
                <div class="header__pane ml-auto">
                    <div>
                        <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                            data-class="closed-sidebar" id="closed-sidebar-btn">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="app-header__mobile-menu">
                <div>
                    <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="app-header__menu">
                <span>
                    <button type="button"
                        class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                        <span class="btn-icon-wrapper">
                            <i class="fa fa-ellipsis-v fa-w-6"></i>
                        </span>
                    </button>
                </span>
            </div>
            <div class="app-header__content">
                <div class="app-header-right">
                    <div class="header-btn-lg pr-0">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- MAIN -->
        <div class="app-main">
            <!-- SIDEBAR -->
            <div class="app-sidebar sidebar-shadow">
                <div class="scrollbar-sidebar">
                    <div class="app-sidebar__inner">
                        <ul class="vertical-nav-menu">
                            {{-- <li>
                                <a href="index.html" id="navbar__dashboard">
                                    <i class="metismenu-icon pe-7s-rocket"></i>
                                    Dashboard
                                </a>
                            </li> --}}
                            <li class="app-sidebar__heading">Dashboards</li>
                            <li>
                                <a href="{{ route('penjualan.index') }}" id="navbar__kasir">
                                    <i class="metismenu-icon pe-7s-cash"></i>
                                    Kasir
                                </a>
                            </li>

                            <li class="app-sidebar__heading">Laporan</li>
                            <li class="">
                                <a href="{{ route('laporan.penjualan.bulanan') }}" id="navbar_laporan"
                                    class="{{ Request::is('laporan/penjualan/bulanan*') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon pe-7s-note2"></i>
                                    Keuangan
                                </a>
                            </li>
                            <li class="">
                                <a href="{{ url('/laporan/kategori') }}" id="navbar_laporan"
                                    class="{{ Request::is('laporan/kategori*') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon pe-7s-note2"></i>
                                    Kategori
                                </a>
                            </li>
                            <li class="">
                                <a href="{{ url('/laporan/barang') }}" id="navbar_laporan"
                                    class="{{ Request::is('laporan/barang*') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon pe-7s-note2"></i>
                                    Barang
                                </a>
                            </li>
                            <li class="app-sidebar__heading">Barang</li>
                            <li>
                                <a href="{{ url('/barang') }}" id="navbar__barang">
                                    <i class="metismenu-icon pe-7s-drawer"></i>
                                    Produk
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/kategori') }}" id="navbar__kategori">
                                    <i class="metismenu-icon pe-7s-folder"></i>
                                    Kategori
                                </a>
                            </li>
                            <li class="">
                                <a href="{{ url('/barang/kadaluarsa') }}" id="navbar_laporan"
                                    class="{{ Request::is('barang/kadaluarsa*') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon pe-7s-note2"></i>
                                    Kadaluarsa
                                </a>
                            </li>
                            <li class="">
                                <a href="{{ url('/laporan/habis') }}" id="navbar_laporan"
                                    class="{{ Request::is('laporan/habis*') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon pe-7s-note2"></i>
                                    Habis
                                </a>
                            </li>
                            <li class="">
                                <a href="{{ url('/barang/cetak-harga') }}" id="navbarCetakHarga"
                                    class="{{ Request::is('barang/cetak-harga*') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon pe-7s-note2"></i>
                                    Cetak Harga
                                </a>
                            </li>

                            <li class="app-sidebar__heading">Belanja</li>
                            <li>
                                <a href="{{ url('/supplier') }}" id="navbar__supplier">
                                    <i class="metismenu-icon pe-7s-car">
                                    </i>Supplier
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/belanja') }}" id="navbar__supplier">
                                    <i class="metismenu-icon pe-7s-car">
                                    </i>Belanja
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- END SIDEBAR -->

            <!-- MAIN Content -->
            <div class="app-main__outer">

                <!-- Section Layouts Content  -->
                @yield('content')
                <!-- End Section Layouts Content -->

                <div class="app-wrapper-footer">
                    <div class="app-footer">
                        <div class="app-footer__inner">
                            Toko Rian
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MAIN Content -->

            <script src="http://maps.google.com/maps/api/js?sensor=true"></script>
        </div>
        <!-- END MAIN -->
        {{-- Modal --}}
        <div class="modal fade modalMain" id="modalMain" tabindex="-1" role="dialog"
            aria-labelledby="modalMainLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        {{-- End modal --}}
    </div>
    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"
        integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <!-- Datatable -->
    <link href="/DataTables/datatables.min.css" rel="stylesheet" />
    <script src="/DataTables/datatables.min.js"></script>
    <script src="/DataTables/Buttons-2.3.6/js/dataTables.buttons.min.js"></script>
    {{-- toastr --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    {{-- Sweetalert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Select2 --}}
    {{-- <script src="{{ asset('assets/template/js/plugin/select2/select2.full.min.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.js"></script>

    {{-- Validate --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"
        integrity="sha512-6S5LYNn3ZJCIm0f9L6BCerqFlQ4f5MwNKq+EthDXabtaJvg3TuFLhpno9pcm+5Ynm6jdA9xfpQoMz2fcjVMk9g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    {{-- html5-qrcode --}}
    <script src="https://unpkg.com/html5-qrcode"></script>

    {{-- Chart JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- JS -->
    <script type="text/javascript" src="{{ asset('assets/scripts/main.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/scripts/script.js') }}"></script>
    <script>
        // click
        $(document).ready(function() {
            setTimeout(() => {
                $("#closed-sidebar-btn").click();
            }, 1000);
        })
    </script>

    {{-- loader --}}
    <script src="{{ asset('assets/js/loader.js') }}"></script>
    <script src="{{ asset('assets/js/empty.js') }}"></script>

    @stack('scripts')
</body>

</html>
