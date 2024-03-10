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
                                @csrf
                                <div class="row">
                                    <label for="daterange" class="col">Rentang Tanggal :</label>
                                    <input type="text" name="daterange" id="daterange" class="form-control mb-3 col">
                                </div>

                                <div class="row">
                                    <label for="month" class="col">Bulan :</label>
                                    <input type="month" name="month" id="month" class="form-control mb-3 col"
                                        @if ($typeReport == 'Bulanan') value="{{ date('Y-m') }}" @endif
                                        onchange="getReportSaleByCategory('bulanan')">
                                </div>
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


        {{-- Chart Category Report --}}
        <div class="row">
            {{-- Chart Laporan Kategori --}}
            <div class="col-lg-6">
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        Laporan Kategori Harian
                    </div>
                    <div class="card-body">
                        <div id="dailyCategoryReportChart"></div>
                    </div>
                </div>
            </div>
            {{-- End hart Laporan Kategori --}}

            {{-- Chart Laporan Kategori --}}
            <div class="col-lg-6">
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        Laporan Kategori Bulanan
                    </div>
                    <div class="card-body">
                        <div id="monthlyCategoryReportChart"></div>
                    </div>
                </div>
            </div>
            {{-- End chart Laporan Kategori --}}
        </div>
        {{-- END Chart Category Report --}}


        {{--  --}}
        <!-- Barang Terjual -->
        <div class="barang__terjual__section">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Riwayat Penjualan</h5>
                    <table class="display nowrap" style="width:100%" id="barang_terjual">
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
            getReportSaleByCategory()
        });

        $(function() {
            $('#daterange').daterangepicker({
                opens: 'right',
                maxDate: moment(),
                ranges: {
                    'Hari Ini': [moment(), moment()],
                    'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                    '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                    'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                    'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'Tahun Ini': [moment().startOf('year'), moment().endOf('year')],
                    'Tahun Kemarin': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1,
                        'year').endOf('year')],
                },
                locale: {
                    format: 'DD/MM/YYYY',
                    separator: ' - ',
                    applyLabel: 'Pilih',
                    cancelLabel: 'Batal',
                    fromLabel: 'Dari',
                    toLabel: 'Ke',
                    customRangeLabel: 'Custom',
                    weekLabel: 'W',
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                        'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ],
                    firstDay: 1
                }
            });

            @if ($typeReport == 'Harian')
                $('#daterange').data('daterangepicker').setStartDate(moment('{{ $date[0] }}', 'YYYY-MM-DD')
                    .format('DD/MM/YYYY'));
                $('#daterange').data('daterangepicker').setEndDate(moment('{{ $date[1] }}', 'YYYY-MM-DD')
                    .format('DD/MM/YYYY'));
            @else
                $('#daterange').val(null);
            @endif

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
                $("#month").val(null);
            });
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val(null);
            });
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

        const getReportSaleByCategory = (typeReport) => {
            if (typeReport == 'harian') {
                $('#month').val(null);
            } else if (typeReport == 'bulanan') {
                $('#daterange').val(null);
            }

            $('#dailyCategoryReportChart').html(inlineLoader())
            $('#monthlyCategoryReportChart').html(inlineLoader())

            $.ajax({
                type: "GET",
                url: `{{ route('report.sale.catgory.data') }}`,
                data: {
                    daterange: $('#daterange').val(),
                    month: $('#month').val()
                },
                success: function(response) {
                    // dailyCategoryReportChart
                    Highcharts.chart('dailyCategoryReportChart', {
                        chart: {
                            type: 'spline'
                        },
                        title: {
                            text: 'Laporan Penjualan Harian ',
                            align: 'center'
                        },

                        subtitle: {
                            text: 'Harian',
                            align: 'center'
                        },

                        yAxis: {
                            title: {
                                text: 'Total Pendapatan'
                            }
                        },

                        xAxis: {
                            title: {
                                text: 'Tanggal'
                            },
                            type: 'datetime', // Menggunakan tipe datetime
                            categories: response.data.categoryByDate[Object.keys(response.data
                                .categoryByDate)[0]].map(category => Date
                                .parse(
                                    category.tanggal)),
                            // Mengonversi tanggal ke timestamp
                            accessibility: {
                                rangeDescription: 'Date'
                            },
                            labels: {
                                format: '{value:%e-%m}', // Menampilkan nilai tanggal
                            }
                        },
                        tooltip: {
                            crosshairs: true,
                            shared: true,
                            formatter: function() {
                                // Sort points by income in descending order
                                var points = this.points.sort(function(a, b) {
                                    return b.y - a.y;
                                });

                                // Build tooltip content with sorted points
                                var tooltipContent = '<b>' + Highcharts.dateFormat(
                                    '%A,%e %b',
                                    this
                                    .x) + '</b><br/>';
                                points.forEach(function(point) {
                                    tooltipContent += point.series.name + ': ' +
                                        formatCurrency(point.y) + '<br/>';
                                });

                                return tooltipContent;
                            }
                        },
                        plotOptions: {
                            series: {
                                label: {
                                    connectorAllowed: true
                                },
                            }
                        },

                        series: Object.entries(response.data.categoryByDate).map(([key,
                            category
                        ]) => ({
                            name: key,
                            data: category.map(transaction => parseInt(transaction
                                .income))
                        })),
                        responsive: {
                            rules: [{
                                condition: {
                                    maxWidth: 500
                                },
                                chartOptions: {
                                    legend: {
                                        layout: 'horizontal',
                                        align: 'center',
                                        verticalAlign: 'bottom'
                                    }
                                }
                            }]
                        }
                    });

                    // monthlyCategoryReportChart
                    Highcharts.chart('monthlyCategoryReportChart', {
                        chart: {
                            type: 'spline'
                        },
                        title: {
                            text: 'Laporan Penjualan Bulanan ',
                            align: 'center'
                        },

                        subtitle: {
                            text: 'Bulanan',
                            align: 'center'
                        },

                        yAxis: {
                            title: {
                                text: 'Jumlah Pendapatan'
                            },
                        },

                        xAxis: {
                            title: {
                                text: 'Bulan'
                            },
                            type: 'datetime', // Menggunakan tipe datetime
                            categories: response.data.categoryByYear[Object.keys(response.data
                                .categoryByYear)[0]].map(category => Date
                                .parse(
                                    category.month)), // Mengonversi tanggal ke timestamp
                            accessibility: {
                                rangeDescription: 'Date'
                            },
                            labels: {
                                format: '{value:%m-%Y}', // Menampilkan nilai tanggal
                            }
                        },
                        tooltip: {
                            crosshairs: true,
                            shared: true,
                            formatter: function() {
                                // Sort points by income in descending order
                                var points = this.points.sort(function(a, b) {
                                    return b.y - a.y;
                                });

                                // Build tooltip content with sorted points
                                var tooltipContent = '<b>' + Highcharts.dateFormat(
                                    '%b-%Y',
                                    this
                                    .x) + '</b><br/>';
                                points.forEach(function(point) {
                                    tooltipContent += point.series.name + ': ' +
                                        formatCurrency(point.y) + '<br/>';
                                });

                                return tooltipContent;
                            }
                        },
                        plotOptions: {
                            series: {
                                label: {
                                    connectorAllowed: true,
                                },
                            }
                        },

                        series: Object.entries(response.data.categoryByYear).map(([key,
                            category
                        ]) => ({
                            name: key,
                            data: category.map(transaction => parseInt(transaction
                                .income))
                        })),
                        responsive: {
                            rules: [{
                                condition: {
                                    maxWidth: 500
                                },
                                chartOptions: {
                                    legend: {
                                        layout: 'horizontal',
                                        align: 'center',
                                        verticalAlign: 'bottom'
                                    }
                                }
                            }]
                        },
                    });
                }
            });
        }
    </script>
@endpush
