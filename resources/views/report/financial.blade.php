@extends('layouts.main')

@section('content')
    <div class="app-main__inner">
        <!-- TITLE -->
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-note2 icon-gradient bg-plum-plate">
                        </i>
                    </div>
                    <div>Laporan Bulanan
                        <div class="page-title-subheading">
                            Laporan
                        </div>
                        <div class="row justify-content-center justify-content-lg-start">
                            <form action="" id="formBulan" class="col-6 col-lg-12">
                                <input type="month" name="laporan_bulan" id="laporan_bulan" class="form-control mb-3"
                                    onchange="laporanBulanan(this)" value="{{ $tanggal }}">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END TITLE -->
        <!-- CARD DASHBOARD -->
        <div class="row">
            <!-- total pendapatan -->
            <div class="col-sm-6 col-md-4 col-xl-3 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-cash col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Pendapatan</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span>Rp. {{ format_uang($report[0]->income) }}</span></div>
                            <div class="perubahan row">
                                {{-- <div class="widget-subheading col-10" id="total_pendapatan">
                                    -2000000
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- total keuntungan -->
            <div class="col-sm-6 col-md-4 col-xl-3 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-graph1 col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Keuntungan</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span>Rp {{ format_uang($report[0]->profit) }}</span></div>
                            <div class="change row" id="change">
                                {{-- <div class="widget-subheading col-10" id="total_keuntungan">
                                    2000000
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- total order -->
            <div class="col-sm-6 col-md-4 col-xl-3 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-news-paper col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Order</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span>{{ format_uang($report[0]->total_transaction) }}</span>
                            </div>
                            <div class="change row" id="change">
                                {{-- <div class="widget-subheading col-10" id="total_order">
                                    -20
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- total barang terjual -->
            <div class="col-sm-6 col-md-4 col-xl-3 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-box2 col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Barang Terjual</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span>{{ format_uang($report[0]->total_item) }}</span></div>
                            <div class="change row" id="change">
                                {{-- <div class="widget-subheading col-10" id="total_barang">
                                    -8
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        {{-- Terlaris --}}
        <div class="row">
            {{-- Jenis terlaris --}}
            <div class="col-md-6">
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        Jenis Terlaris
                    </div>
                    <div class="table-responsive">
                        <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Nama</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jenisTerlaris as $jenis)
                                    <tr>
                                        <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="widget-content p-0">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left flex2">
                                                        {{ $jenis->jenis }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $jenis->total }}</td>
                                        <td class="text-center">
                                            <a href="">
                                                <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm">
                                                    Details
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- End Jenis terlaris --}}
            {{-- Barang terlaris --}}
            <div class="col-md-6">
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        Barang Terlaris
                    </div>
                    <div class="table-responsive">
                        <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Nama</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barangTerlaris as $barang)
                                    <tr>
                                        <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="widget-content p-0">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left flex2">
                                                        {{ $barang->namaBarang }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $barang->total }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('laporan.barang.bulanan', $barang->idBarang) }}">
                                                <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm">
                                                    Details
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- End Barang terlaris --}}
        </div>
        {{-- END Terlaris --}}
        <!-- END CARD DASHBOARD -->
        {{--  --}}
        <!-- Barang Terjual -->
        <div class="barang__terjual__section">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Riwayat Penjualan</h5>
                    <table class="mb-0 table" id="barang_terjual">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Kasir</th>
                                <th>Total Item</th>
                                <th>Total Harga</th>
                                <th>Keuntungan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kasir as $kasir)
                                <tr>
                                    <td scope="row">{{ $kasir->tanggal }}</td>
                                    <td>{{ $kasir->noTransaksi }}</td>
                                    <td>{{ $kasir->jumlah }}</td>
                                    <td>{{ $kasir->total }}</td>
                                    <td>{{ $kasir->laba }}</td>
                                    <td>
                                        <a href="{{ route('laporan.show', $kasir->noTransaksi) }}"
                                            class="btn btn-primary">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end barang terjual -->

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#laporan_bulanan").DataTable({
                pageLength: 3,
                paging: false,
                info: false,
            });
        });

        function laporanBulanan(input) {
            let formBulan = $("#formBulan");
            formBulan.submit();
            // Upload log ke server
        }
    </script>
@endpush
