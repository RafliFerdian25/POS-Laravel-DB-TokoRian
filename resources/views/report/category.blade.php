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
                                        onchange="getCategoriesData('bulanan'); getReportSaleByCategory('bulanan')">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END TITLE -->

        {{-- Terlaris --}}
        <div class="chartCategorySection">
            {{-- Pernjualan Berdasarkan Kategori --}}
            <div class="main-card mb-3 card">
                <div class="card-header">
                    Pernjualan Berdasarkan Kategori
                </div>
                <div class="card-body">
                    <div>
                        <div id="categoryChart"></div>
                    </div>
                </div>
            </div>
            {{-- End Pernjualan Berdasarkan Kategori --}}
        </div>
        {{-- END Terlaris --}}

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
        <!-- Jenis Terlaris -->
        <div class="bestSellingCategorySection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Jenis Terlaris</h5>
                    <table class="display nowrap" style="width:100%" id="tableBestSellingCategory">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Jumlah Terjual</th>
                                <th class="text-center">Total Penjualan</th>
                                <th class="text-center">Total Keuntungan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBestSellingCategoryBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end Jenis Terlaris -->

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var configDataTable = {
                "columnDefs": [{
                    "targets": [1, 2, 3, 4, 5],
                    "className": "text-center"
                }, {
                    // Mengatur aturan pengurutan kustom untuk kolom keempat (index 3)
                    "targets": [3, 4],
                    "render": function(data, type, row) {
                        // Memeriksa tipe data, jika tampilan atau filter
                        if (type === 'display' || type === 'filter') {
                            // Memformat angka menggunakan fungsi formatCurrency
                            return formatCurrency(data);
                        }
                        // Jika tipe data selain tampilan atau filter, kembalikan data tanpa perubahan
                        return data;
                    }
                }],
            }
            initializeDataTable("tableBestSellingCategory", configDataTable)

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
                getCategoriesData('harian')
                getReportSaleByCategory('harian')
            });
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val(null);
            });
        });

        // Chart
        const ctx = document.getElementById('categoryChart');
        // Declare a global variable to store the Chart instance
        let categoryChart;

        const getCategoriesData = (typeReport) => {
            if (typeReport == 'harian') {
                $('#month').val(null);
            } else if (typeReport == 'bulanan') {
                $('#daterange').val(null);
            }
            if (categoryChart) {
                categoryChart.destroy();
            }
            $('#categoryChart').html(inlineLoader())
            $('#tableBestSellingCategoryBody').html(tableLoader(6))

            $.ajax({
                type: "GET",
                url: "{{ route('laporan.kategori.data') }}",
                data: $("#formBulan").serialize(),
                success: function(response) {
                    (function(H) {
                        H.seriesTypes.pie.prototype.animate = function(init) {
                            const series = this,
                                chart = series.chart,
                                points = series.points,
                                {
                                    animation
                                } = series.options,
                                {
                                    startAngleRad
                                } = series;

                            function fanAnimate(point, startAngleRad) {
                                const graphic = point.graphic,
                                    args = point.shapeArgs;

                                if (graphic && args) {

                                    graphic
                                        // Set inital animation values
                                        .attr({
                                            start: startAngleRad,
                                            end: startAngleRad,
                                            opacity: 1
                                        })
                                        // Animate to the final position
                                        .animate({
                                            start: args.start,
                                            end: args.end
                                        }, {
                                            duration: animation.duration / points.length
                                        }, function() {
                                            // On complete, start animating the next point
                                            if (points[point.index + 1]) {
                                                fanAnimate(points[point.index + 1], args.end);
                                            }
                                            // On the last point, fade in the data labels, then
                                            // apply the inner size
                                            if (point.index === series.points.length - 1) {
                                                series.dataLabelsGroup.animate({
                                                        opacity: 1
                                                    },
                                                    void 0,
                                                    function() {
                                                        points.forEach(point => {
                                                            point.opacity = 1;
                                                        });
                                                        series.update({
                                                            enableMouseTracking: true
                                                        }, false);
                                                        chart.update({
                                                            plotOptions: {
                                                                pie: {
                                                                    innerSize: '40%',
                                                                    borderRadius: 8
                                                                }
                                                            }
                                                        });
                                                    });
                                            }
                                        });
                                }
                            }

                            if (init) {
                                // Hide points on init
                                points.forEach(point => {
                                    point.opacity = 0;
                                });
                            } else {
                                fanAnimate(points[0], startAngleRad);
                            }
                        };
                    }(Highcharts));


                    var totalJumlah = response.data.reports.reduce((total, report) => total + parseInt(
                        report.jumlah), 0);

                    var dataChart = response.data.reports.map(report => ({
                        name: report.keterangan,
                        y: parseInt(report.jumlah),
                        percentage: (parseInt(report.jumlah) / totalJumlah) * 100,
                    }));

                    Highcharts.chart('categoryChart', {
                        chart: {
                            type: 'pie'
                        },
                        title: {
                            text: 'PERNJUALAN BERDASARKAN KATEGORI',
                            align: 'center'
                        },
                        subtitle: {
                            text: 'penjualan barang berdasarkan kategori',
                            align: 'center'
                        },
                        tooltip: {
                            pointFormat: 'Jumlah:<b>{point.y}</b><br>Persen: <b>{point.percentage:.1f}%</b>'
                        },
                        accessibility: {
                            point: {
                                valueSuffix: '%'
                            }
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                borderWidth: 2,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b><br>{point.y} ({point.percentage:.1f}%)',
                                    distance: 20
                                }
                            }
                        },
                        series: [{
                            // Disable mouse tracking on load, enable after custom animation
                            enableMouseTracking: false,
                            animation: {
                                duration: 2000
                            },
                            colorByPoint: true,
                            data: dataChart
                        }]
                    });

                    $('#tableBestSellingCategory').DataTable().clear().draw();
                    if (response.data.bestSellingCategories.length > 0) {
                        $.each(response.data.bestSellingCategories, function(index, category) {
                            var rowData = [
                                index + 1,
                                category.name,
                                category.total,
                                category.income,
                                category.profit,
                                `<a href="{{ url('/laporan/kategori/${category.id}/detail') }}" class="btn btn-sm btn-warning"">Detail</button>`
                            ];
                            var rowNode = $('#tableBestSellingCategory').DataTable().row.add(
                                    rowData)
                                .draw(false)
                                .node();
                        });
                    } else {
                        $('#tableBestSellingCategoryBody').html(tableEmpty(6,
                            'Riwayat Penjualan'));
                    }
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
