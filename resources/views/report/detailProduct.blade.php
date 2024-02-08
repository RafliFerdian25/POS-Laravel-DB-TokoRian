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
                    <div>Laporan Bulanan {{ $barang->nmBarang }}
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
            <div class="col-sm-6 col-md-4 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-cash col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Pendapatan</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span>Rp. {{ format_uang($report->income) }}</span></div>
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
            <div class="col-sm-6 col-md-4 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-graph1 col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Keuntungan</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span>Rp {{ format_uang($report->profit) }}</span></div>
                            <div class="change row" id="change">
                                {{-- <div class="widget-subheading col-10" id="total_keuntungan">
                                    2000000
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- total barang terjual -->
            <div class="col-sm-6 col-md-4 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-box2 col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Barang Terjual</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span>{{ format_uang($report->total_item) }}</span></div>
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
        <!-- END CARD DASHBOARD -->

        <div class="row">
            {{-- Detail Barang --}}
            <div class="col-md-6">
                <div class="main-card mb-3 card">
                    <div class="card-header-tab card-header">
                        <div class="card-header-title">
                            <i class="header-icon lnr-rocket icon-gradient bg-tempting-azure">
                            </i>
                            DETAIL BARANG
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Nama Barang</td>
                                    <td>:</td>
                                    <td>{{ $barang->nmBarang }}</td>
                                </tr>
                                <tr>
                                    <td>Jenis</td>
                                    <td>:</td>
                                    <td>{{ $barang->type->keterangan }}</td>
                                </tr>
                                <tr>
                                    <td>Harga Beli</td>
                                    <td>:</td>
                                    <td>{{ format_uang($barang->hargaPokok) }}</td>
                                </tr>
                                <tr>
                                    <td>Harga Jual</td>
                                    <td>:</td>
                                    <td>{{ format_uang($barang->hargaJual) }}</td>
                                </tr>
                                <tr>
                                    <td>Stok</td>
                                    <td>:</td>
                                    <td>{{ $barang->stok }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- END DETAIL BARANG --}}
            {{-- GRAFIK PENJUALAN BULAN --}}
            <div class="col-md-12 col-lg-6">
                <div class="mb-3 card">
                    <div class="card-header-tab card-header">
                        <div class="card-header-title">
                            <i class="header-icon lnr-rocket icon-gradient bg-tempting-azure">
                            </i>
                            Penjualan Bulanan
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="tab-eg-55">
                            <div class="widget-chart p-3">
                                <canvas id="ChartBulanan" class="h-150"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- END GRAFIK PENJUALAN BULAN --}}
        </div>

        {{-- GRAFIK PENJUALAN --}}
        <div class="row">
            <div class="col-12">
                <div class="mb-3 card">
                    <div class="card-header-tab card-header">
                        <div class="card-header-title">
                            <i class="header-icon lnr-rocket icon-gradient bg-tempting-azure">
                            </i>
                            Penjualan Bulanan
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="tab-eg-55">
                            <div class="widget-chart p-3">
                                <canvas id="ChartHarian" class="h-300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- ENDGRAFIK PENJUALAN --}}
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td scope="row">{{ $transaction->tanggal }}</td>
                                    <td>{{ $transaction->noTransaksi }}</td>
                                    <td>{{ $transaction->jumlah }}</td>
                                    <td>{{ $transaction->total }}</td>
                                    <td>{{ $transaction->laba }}</td>
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

        // chart bulan
        let months = [];
        let incomeMonth = [];
        @foreach ($reportMonths as $reportMonth)
            months.push('{{ $reportMonth->month }}');
            incomeMonth.push('{{ $reportMonth->income }}');
        @endforeach
        // data
        const DataPenjualanBulanan = {
            labels: months,
            datasets: [{
                label: "Pendapatan",
                fill: !0,
                backgroundColor: "#A7C4BC",
                borderColor: "#2F5D62",
                borderCapStyle: "butt",
                borderDash: [],
                borderDashOffset: 0,
                pointBorderColor: "#2F5D62",
                pointBackgroundColor: "#2F5D62",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "#2F5D62",
                pointHoverBorderColor: "#2F5D62",
                pointHoverBorderWidth: 1,
                pointRadius: 3,
                pointHitRadius: 5,
                data: incomeMonth,
            }]
        }
        // config
        const annualFinancialConfig = {
            type: 'line',
            data: DataPenjualanBulanan,
            options: {
                maintainAspectRatio: !1,
                legend: {
                    display: true
                },
                animation: {
                    easing: "easeInOutBack"
                },
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            fontColor: "#ACB1D6",
                            fontStyle: "bold",
                            beginAtZero: !0,
                            maxTicksLimit: 10,
                            padding: 0
                        },
                        gridLines: {
                            drawTicks: !1,
                            display: !1
                        }
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: !1,
                            zeroLineColor: "transparent"
                        },
                        ticks: {
                            padding: 0,
                            fontColor: "#ACB1D6",
                            fontStyle: "bold"
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            // Format the tooltip label as Indonesian Rupiah without trailing zeros
                            var value = tooltipItem.yLabel;
                            var formattedValue = new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0
                            }).format(value);
                            return "Pendapatan: " + formattedValue;
                        }
                    },
                },
            },
            responsive: true,
            maintainAspectRatio: false
        }
        // define
        const ChartBulanan = document.getElementById('ChartBulanan').getContext('2d');
        new Chart(ChartBulanan, annualFinancialConfig);

        // chart harian
        let days = [];
        let incomeDay = [];
        @foreach ($reportDays as $reportDay)
            days.push('{{ $reportDay->day }}');
            incomeDay.push('{{ $reportDay->income }}');
        @endforeach
        // data
        const DataPenjualanHarian = {
            labels: days,
            datasets: [{
                label: "Pendapatan",
                fill: !0,
                backgroundColor: "#A7C4BC",
                borderColor: "#2F5D62",
                borderCapStyle: "butt",
                borderDash: [],
                borderDashOffset: 0,
                pointBorderColor: "#2F5D62",
                pointBackgroundColor: "#2F5D62",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "#2F5D62",
                pointHoverBorderColor: "#2F5D62",
                pointHoverBorderWidth: 1,
                pointRadius: 3,
                pointHitRadius: 5,
                data: incomeDay,
            }]
        }
        // config
        const ConfigPenjualanHarian = {
            type: 'line',
            data: DataPenjualanHarian,
            options: {
                maintainAspectRatio: !1,
                legend: {
                    display: true
                },
                animation: {
                    easing: "easeInOutBack"
                },
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            fontColor: "#ACB1D6",
                            fontStyle: "bold",
                            beginAtZero: !0,
                            maxTicksLimit: 10,
                            padding: 0
                        },
                        gridLines: {
                            drawTicks: !1,
                            display: !1
                        }
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: !1,
                            zeroLineColor: "transparent"
                        },
                        ticks: {
                            padding: 0,
                            fontColor: "#ACB1D6",
                            fontStyle: "bold"
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            // Format the tooltip label as Indonesian Rupiah without trailing zeros
                            var value = tooltipItem.yLabel;
                            var formattedValue = new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0
                            }).format(value);
                            return "Pendapatan: " + formattedValue;
                        }
                    },
                }
            }
        }
        // define
        const ChartHarian = document.getElementById('ChartHarian').getContext('2d');
        new Chart(ChartHarian, ConfigPenjualanHarian);
    </script>
@endpush
