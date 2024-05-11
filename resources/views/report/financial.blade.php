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
                    <div>Laporan Penjualan
                        <div class="page-title-subheading">
                            Laporan keuangan <span class="fw-bold" id="dateString"></span>
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
                                        onchange="getSaleReport('bulanan'); getSaleReportByCategory('bulanan'); getExpenses('bulanan')">
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
                            <div class="widget-numbers mb-2"><span id="income">Rp. </span></div>
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
                            <div class="widget-numbers mb-2"><span id="profit">Rp. </span></div>
                            <div class="change row" id="change">
                                {{-- <div class="widget-subheading col-10" id="total_keuntungan">
                                    2000000
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- total Transaksi -->
            <div class="col-sm-6 col-md-4 col-xl-3 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-news-paper col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Transaksi</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="total_transaction"></span>
                            </div>
                            <div class="change row" id="change">
                                {{-- <div class="widget-subheading col-10" id="total_Transaksi">
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
                            <div class="widget-numbers mb-2"><span id="total_product"></span></div>
                            <div class="change row" id="change">
                                {{-- <div class="widget-subheading col-10" id="total_barang">
                                    -8
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Pembelian -->
            <div class="col-sm-6 col-md-4 col-xl-3 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-cash col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Pembelian</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="purchase"></span></div>
                            <div class="change row" id="change">
                                {{-- <div class="widget-subheading col-10" id="total_barang">
                                    -8
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- total pembelian barang -->
            <div class="col-sm-6 col-md-4 col-xl-3 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-box2 col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Pembelian Barang</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="total_purchase_product"></span></div>
                            <div class="change row" id="change">
                                {{-- <div class="widget-subheading col-10" id="total_barang">
                                    -8
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Pengeluaran -->
            <div class="col-sm-6 col-md-4 col-xl-3 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-cash col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Pengeluaran</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="expense"></span></div>
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
                        <table class="align-middle table table-borderless table-striped table-hover"
                            id="tableBestSellingCategories">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Nama</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- End Jenis terlaris --}}
            {{-- Barang terlaris --}}
            <div class="col-md-6">
                <div class="main-card mb-3 card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            Barang Terlaris
                        </div>
                        <div>
                            <a href="{{ route('monthly.product.report') }}" class="btn btn-primary"> </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="align-middle table table-borderless table-striped table-hover"
                            id="tableBestSellingProducts">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Nama</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBestSellingProductsBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- End Barang terlaris --}}
        </div>
        {{-- END Terlaris --}}
        <!-- END CARD DASHBOARD -->

        {{-- Chart Sale Report --}}
        <div class="row">
            {{-- Chart Laporan Penjualan --}}
            <div class="col-lg-6">
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        Laporan Penjualan Harian
                    </div>
                    <div class="card-body">
                        <div id="dailyFinancialReportChart"></div>
                    </div>
                </div>
            </div>
            {{-- End hart Laporan Penjualan --}}

            {{-- Chart Laporan Penjualan --}}
            <div class="col-lg-6">
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        Laporan Penjualan Bulanan
                    </div>
                    <div class="card-body">
                        <div id="monthlyFinancialReportChart"></div>
                    </div>
                </div>
            </div>
            {{-- End chart Laporan Penjualan --}}
        </div>
        {{-- END Chart Sale Report --}}

        <!-- Barang Terjual -->
        <div class="barang__terjual__section">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Riwayat Penjualan</h5>
                    <table class="display nowrap" style="width:100%" id="transactionByNoTransactions">
                        <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Tanggal</th>
                                <th>Total Item</th>
                                <th>Total Pendapatan</th>
                                <th>Keuntungan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="transactionByNoTransactionsBody">
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
            var configDataTable = {
                "columnDefs": [{
                    "targets": "_all",
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
                "order": [
                    [1, "desc"]
                ]
            }
            initializeDataTable("transactionByNoTransactions", configDataTable);
            getSaleReport()
            getExpenses()
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
                getSaleReport('harian');
                getExpenses('harian')
            });
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val(null);
            });
        });

        const getSaleReport = (typeReport) => {
            if (typeReport == 'harian') {
                $('#month').val(null);
            } else if (typeReport == 'bulanan') {
                $('#daterange').val(null);
            }

            $('#income').html(inlineLoader())
            $('#profit').html(inlineLoader())
            $('#total_transaction').html(inlineLoader())
            $('#total_product').html(inlineLoader())
            $('#purchase').html(inlineLoader())
            $('#total_purchase_product').html(inlineLoader())

            $('#tableBestSellingCategories tbody').html(tableLoader(4))
            $('#tableBestSellingProducts tbody').html(tableLoader(4))
            $('#transactionByNoTransactionsBody').html(tableLoader(6))

            var daterange = $('#daterange').val();
            var month = $('#month').val();

            $.ajax({
                type: "GET",
                url: `{{ url('laporan/penjualan/data') }}`,
                data: {
                    daterange: daterange,
                    month: month
                },
                success: function(response) {
                    $('#income').text(formatCurrency(response.data.report.income));
                    $('#profit').text(formatCurrency(response.data.report.profit));
                    $('#total_transaction').text(response.data.report.total_transaction);
                    $('#total_product').text(response.data.report.total_product);
                    $('#purchase').text(formatCurrency(response.data.reportPurchase.purchase));
                    $('#total_purchase_product').text(response.data.reportPurchase.total_purchase_product);

                    $('#tableBestSellingCategories tbody').empty();
                    $('#tableBestSellingProducts tbody').empty();

                    getBestSellingProduct(daterange, month).then((response) => {
                        // Memanggil fungsi untuk memproses data produk terlaris
                        const bestSellingProductData = response.data.bestSellingProducts;
                        processDataBestSelling(bestSellingProductData, 'tableBestSellingProducts');
                    }).catch((error) => {
                        console.error('Error:', error);
                    });

                    getBestSellingCategory(daterange, month).then((response) => {
                        // Memanggil fungsi untuk memproses data kategori terlaris
                        const bestSellingCategoryData = response.data.bestSellingCategories;
                        processDataBestSelling(bestSellingCategoryData,
                            'tableBestSellingCategories');
                    }).catch((error) => {
                        console.error('Error:', error);
                    });

                    getTransactionByNoTransactions(daterange, month).then((response) => {
                            // table data barang terjual
                            $('#transactionByNoTransactions').DataTable().clear().draw();
                            const transactionByNoTransactions = response.data
                                .transactionByNoTransactions;
                            if (transactionByNoTransactions.length > 0) {
                                $.each(transactionByNoTransactions, function(index,
                                    transaction) {
                                    var rowData = [
                                        transaction.no_transaction,
                                        transaction.date,
                                        transaction.total_product,
                                        transaction.income,
                                        transaction.profit,
                                        `<a href="{{ url('/laporan/penjualan/detail?id=${transaction.no_transaction}') }}" class="btn btn-sm btn-warning" >Detail</a>`
                                    ];
                                    var rowNode = $('#transactionByNoTransactions').DataTable()
                                        .row
                                        .add(
                                            rowData)
                                        .draw(
                                            false)
                                        .node();
                                });
                            } else {
                                $('#transactionByNoTransactionsBody').html(tableEmpty(6,
                                    'Riwayat Penjualan'));
                            }
                        })
                        .catch((error) => {
                            // Menangani kesalahan jika terjadi
                            console.error('Error:', error);
                        });

                    // dailyFinancialReportChart
                    Highcharts.chart('dailyFinancialReportChart', {
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
                                text: 'Jumlah Penjualan'
                            }
                        },

                        xAxis: {
                            title: {
                                text: 'Tanggal'
                            },
                            type: 'datetime', // Menggunakan tipe datetime
                            categories: response.data.transactionsByDate.map(transaction => Date
                                .parse(
                                    transaction.tanggal)), // Mengonversi tanggal ke timestamp
                            accessibility: {
                                rangeDescription: 'Date'
                            },
                            labels: {
                                format: '{value:%e}', // Menampilkan nilai tanggal
                            }
                        },
                        tooltip: {
                            crosshairs: true,
                            shared: true,
                        },
                        plotOptions: {
                            series: {
                                label: {
                                    connectorAllowed: true
                                },
                            }
                        },

                        series: [{
                            name: 'Total Pendapatan',
                            data: response.data.transactionsByDate.map(transaction =>
                                parseInt(
                                    transaction
                                    .income)),
                        }, {
                            name: 'Total Keuntungan',
                            data: response.data.transactionsByDate.map(transaction =>
                                parseInt(
                                    transaction
                                    .profit))
                        }, {
                            name: 'Jumlah Barang Terjual',
                            data: response.data.transactionsByDate.map(transaction =>
                                parseInt(
                                    transaction
                                    .total_product)),
                        }, {
                            name: 'Rata - Rata Pendapatan',
                            visible: false,
                            data: response.data.transactionsByDate.map(transaction =>
                                calculateAverage(
                                    response.data.transactionsByDate
                                    .map(transaction => parseInt(transaction
                                        .income))
                                )),
                        }, {
                            name: 'Rata - Rata Keuntungan',
                            visible: false,
                            data: response.data.transactionsByDate.map(transaction =>
                                calculateAverage(
                                    response.data.transactionsByDate
                                    .map(transaction => parseInt(transaction
                                        .profit))
                                )),
                        }, {
                            name: 'Rata - Rata Barang Terjual',
                            visible: false,
                            data: response.data.transactionsByDate.map(transaction =>
                                calculateAverage(
                                    response.data.transactionsByDate
                                    .map(transaction => parseInt(transaction
                                        .total_product))
                                )),
                        }],

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

                    // monthlyFinancialReportChart
                    Highcharts.chart('monthlyFinancialReportChart', {
                        chart: {
                            type: 'spline'
                        },
                        title: {
                            text: 'Laporan Penjualan Bulanan',
                            align: 'center'
                        },

                        subtitle: {
                            text: 'Bulanan',
                            align: 'center'
                        },

                        yAxis: {
                            title: {
                                text: 'Jumlah Penjualan'
                            },
                        },

                        xAxis: {
                            title: {
                                text: 'Bulan'
                            },
                            type: 'datetime', // Menggunakan tipe datetime
                            categories: response.data.transactionsByYear.map(transaction =>
                                transaction.month), // Mengonversi tanggal ke timestamp
                            accessibility: {
                                rangeDescription: 'Date'
                            },
                            labels: {
                                format: '{value:%m-%Y}', // Menampilkan nilai tanggal
                            }
                        },
                        tooltip: {
                            crosshairs: true,
                            shared: true
                        },
                        plotOptions: {
                            series: {
                                label: {
                                    connectorAllowed: true,
                                },
                            }
                        },

                        series: [{
                            name: 'Total Pendapatan',
                            data: response.data.transactionsByYear.map(transaction =>
                                parseInt(
                                    transaction
                                    .income)),
                        }, {
                            name: 'Total Keuntungan',
                            data: response.data.transactionsByYear.map(transaction =>
                                parseInt(
                                    transaction
                                    .profit))
                        }, {
                            name: 'Jumlah Barang Terjual',
                            data: response.data.transactionsByYear.map(transaction =>
                                parseInt(
                                    transaction
                                    .total_product)),
                        }, {
                            name: 'Rata - Rata Pendapatan',
                            visible: false,
                            data: response.data.transactionsByYear.map(transaction =>
                                calculateAverage(
                                    response.data.transactionsByYear
                                    .map(transaction => parseInt(transaction
                                        .income))
                                )),
                        }, {
                            name: 'Rata - Rata Keuntungan',
                            visible: false,
                            data: response.data.transactionsByYear.map(transaction =>
                                calculateAverage(
                                    response.data.transactionsByYear
                                    .map(transaction => parseInt(transaction
                                        .profit))
                                )),
                        }, {
                            name: 'Rata - Rata Barang Terjual',
                            visible: false,
                            data: response.data.transactionsByYear.map(transaction =>
                                calculateAverage(
                                    response.data.transactionsByYear
                                    .map(transaction => parseInt(transaction
                                        .total_product))
                                )),

                        }],
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
        };

        const getBestSellingProduct = (daterange, month) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: "GET",
                    url: "{{ route('best.selling.product.report.data') }}",
                    data: {
                        daterange: daterange,
                        month: month
                    },
                    success: function(response) {
                        resolve(response)

                    },
                    error: function(err) {
                        reject(err)
                        $('#tableBestSellingProductsBody').html("Gagal memuat data");
                    }
                });
            });
        }

        const getBestSellingCategory = (daterange, month) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: "GET",
                    url: "{{ route('best.selling.category.report.data') }}",
                    data: {
                        daterange: daterange,
                        month: month
                    },
                    success: function(response) {
                        resolve(response)
                    },
                    error: function(err) {
                        reject(err)
                        $('#tableBestSellingProductsBody').html("Gagal memuat data");
                    }
                });
            });
        }

        // Mengelola hasil dari kedua panggilan Promise
        const processDataBestSelling = (data, tableId) => {
            const tableBody = $(`#${tableId} tbody`);
            if (data.length > 0) {
                data.forEach((item, index) => {
                    tableBody.append(`
                        <tr>
                            <td class="text-center text-muted">${index + 1}</td>
                            <td>
                                <div class="widget-content p-0">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-content-left flex2">
                                            ${item.name}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">${item.total}</td>
                            <td class="text-center">
                                <a href="{{ url('/laporan/barang/${item.id}') }}">
                                    <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm">
                                        Detail
                                    </button>
                                </a>
                            </td>
                        </tr>
                    `);
                });
            } else {
                tableBody.html(tableEmpty(4, 'barang terlaris'));
            }
        };

        const getTransactionByNoTransactions = (daterange, month) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: "GET",
                    url: "{{ route('sale.report.transaction.data') }}",
                    data: {
                        daterange: daterange,
                        month: month
                    },
                    success: function(response) {
                        resolve(response)
                    },
                    error: function(err) {
                        reject(err)
                        $('#transactionByNoTransactionsBody').html("Gagal memuat data");
                    }
                });
            });
        }

        const getExpenses = (typeReport) => {
            if (typeReport == 'harian') {
                $('#month').val(null);
            } else if (typeReport == 'bulanan') {
                $('#daterange').val(null);
            }

            $.ajax({
                type: "GET",
                url: `{{ route('expense.sum.data') }}`,
                data: {
                    daterange: $('#daterange').val(),
                    month: $('#month').val()
                },
                success: function(response) {
                    $('#expense').text(formatCurrency(response.data.expense));
                }
            });
        }
    </script>
@endpush
