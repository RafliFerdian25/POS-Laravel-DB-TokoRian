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
                            <form id="formBulan" class="col-6 col-lg-12">
                                <input type="month" name="reportDate" id="reportDate" class="form-control mb-3"
                                    onchange="laporanBulanan(this)" value="{{ now()->format('Y-m') }}">
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
                            <div class="widget-numbers mb-2"><span>Rp. {{ format_uang(100000) }}</span></div>
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
                            <div class="widget-numbers mb-2"><span>Rp {{ format_uang(100000) }}</span></div>
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
                            <div class="widget-numbers mb-2"><span>{{ format_uang(100000) }}</span>
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
                            <div class="widget-numbers mb-2"><span>{{ format_uang(100000) }}</span></div>
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
        <div class="chartCategorySection">
            {{-- Jenis terlaris --}}
            <div class="main-card mb-3 card">
                <div class="card-header">
                    Jenis Terlaris
                </div>
                <div class="card-body">
                    <div>
                        <canvas id="categoryChart" width="400" height="100"></canvas>
                    </div>
                </div>
            </div>
            {{-- End Jenis terlaris --}}
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
                            <tr>
                                <td scope="row">10</td>
                                <td>10</td>
                                <td>10</td>
                                <td>10</td>
                                <td>10</td>
                                <td>
                                    <a href="{{ route('laporan.show', 1) }}" class="btn btn-primary">Detail</a>
                                </td>
                            </tr>
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
            getCategoriesData()
        });

        function laporanBulanan(input) {
            getCategoriesData()
        }

        // Chart
        const ctx = document.getElementById('categoryChart');
        // Declare a global variable to store the Chart instance
        let categoryChart;

        const getCategoriesData = () => {
            if (categoryChart) {
                categoryChart.destroy();
            }
            $.ajax({
                type: "GET",
                url: "{{ route('laporan.kategori.data') }}",
                data: $("#formBulan").serialize(),
                success: function(response) {
                    categoryChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: response.data.reports.map(report => report.keterangan),
                            datasets: [{
                                label: '# of Votes',
                                data: response.data.reports.map(report => report.jumlah),
                                backgroundColor: [
                                    'rgb(255, 99, 132)',
                                    'rgb(54, 162, 235)',
                                    'rgb(255, 205, 86)',
                                    'rgb(75, 192, 192)',
                                    'rgb(153, 102, 255)',
                                    'rgb(255, 159, 64)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.log(xhr);
                }
            });
        }
    </script>
@endpush
