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
                    <div>Laporan Kategori <span class="fw-bold">{{ $category->keterangan }}</span> <span
                            id="typeReportTitleHeading"></span>
                        <div class="page-title-subheading">
                            Laporan penjualan <span class="typeReportTitleSubHeading"></span>
                            <span class="fw-bold" id="dateString"></span>
                        </div>
                        <div class="row justify-content-center justify-content-lg-start">
                            <form action="" id="formBulan" class="col-6 col-lg-12">
                                @csrf
                                <div class="row">
                                    <label for="daterange" class="col">Rentang Tanggal :</label>
                                    <input type="text" name="daterange" id="daterange" class="form-control mb-3 col">
                                </div>
                                <div class="row">
                                    <label for="month" class="col">Bulan :</label>
                                    <input type="month" name="month" id="month" class="form-control mb-3 col"
                                        @if ($typeReport == 'Bulanan') value="{{ date('Y-m') }}" @endif
                                        onchange="getReportCategory('bulanan')">
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
                            <div class="widget-numbers mb-2"><span id="profit">Rp </span></div>
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
                            <div class="widget-numbers mb-2"><span id="total_transaction">0</span>
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
                            <div class="widget-numbers mb-2"><span id="total_product">0</span></div>
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
            <div class="col-md-8">
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        Jenis Terlaris
                    </div>
                    <div class="card-body">
                        <div id="chartReport"></div>
                    </div>
                </div>
            </div>
            {{-- End Jenis terlaris --}}
            {{-- Barang terlaris --}}
            <div class="col-md-4">
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
                        <table class="align-middle mb-0 table table-borderless table-striped table-hover"
                            id="tableBestSellingProducts">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Nama</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center text-muted"></td>
                                    <td>
                                        <div class="widget-content p-0">
                                            <div class="widget-content-wrapper">
                                                <div class="widget-content-left flex2">

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"></td>
                                    <td class="text-center">
                                        <a href="">
                                            <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm">
                                                Details
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- End Barang terlaris --}}
        </div>
        {{-- END Terlaris --}}
        <!-- END CARD DASHBOARD -->

        <!-- Barang Terjual -->
        <div class="productSaleTransactionSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Riwayat Penjualan</h5>
                    <table class="display nowrap" style="100%" id="tableProductSaleTransaction">
                        <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Tanggal</th>
                                <th>Total Item</th>
                                <th>Total Harga</th>
                                <th>Keuntungan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableProductSaleTransactionBody">
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
            $("#tableProductSaleTransaction").DataTable({
                // scrollX: true,
            });

            getReportCategory()
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
                getReportCategory('harian')
            });
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val(null);
            });
        });

        const getReportCategory = (typeReport) => {
            if (typeReport == 'harian') {
                $('#month').val(null);
            } else if (typeReport == 'bulanan') {
                $('#daterange').val(null);
            }

            $('#income').html(inlineLoader())
            $('#profit').html(inlineLoader())
            $('#total_transaction').html(inlineLoader())
            $('#total_product').html(inlineLoader())
            $('#tableBestSellingProducts tbody').html(tableLoader(4))
            $('#tableProductSaleTransactionBody').html(tableLoader(6))

            $.ajax({
                type: "GET",
                url: `{{ url('laporan/kategori/detail/' . $category->jenis . '/data') }}`,
                data: {
                    daterange: $('#daterange').val(),
                    month: $('#month').val()
                },
                success: function(response) {
                    $('#income').text(formatCurrency(response.data.report.income));
                    $('#profit').text(formatCurrency(response.data.report.profit));
                    $('#total_transaction').text(response.data.report.total_transaction);
                    $('#total_product').text(response.data.report.total_product);
                    $('#tableBestSellingProducts tbody').empty();

                    // table data barang terjual terbaik
                    response.data.bestSellingProducts.forEach((item, index) => {
                        $('#tableBestSellingProducts tbody').append(`
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
                                <a href="">
                                    <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm">
                                        Details
                                    </button>
                                </a>
                            </td>
                        </tr>
                    `);
                    });

                    // chart
                    Highcharts.chart('chartReport', {
                        title: {
                            text: 'Laporan Penjualan Kategori {{ $category->keterangan }}',
                            align: 'center'
                        },

                        subtitle: {
                            text: 'Bulanan',
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
                                    .total)),
                        }, {
                            name: 'Total Keuntungan',
                            data: response.data.transactionsByDate.map(transaction =>
                                parseInt(
                                    transaction
                                    .profit))
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

                    // table data barang terjual
                    $('#tableProductSaleTransaction').DataTable().clear().draw();
                    if (response.data.transactionsByNoTransaction.length > 0) {
                        $.each(response.data.transactionsByNoTransaction, function(index, transaction) {
                            var rowData = [
                                transaction.noTransaksi,
                                transaction.tanggal,
                                transaction.total_product,
                                transaction.income,
                                transaction.profit,
                                `<button class="btn btn-sm btn-warning" onclick="showEdit('${transaction.noTransaksi}')">Detail</button>`
                            ];
                            var rowNode = $('#tableProductSaleTransaction').DataTable().row.add(
                                    rowData)
                                .draw(
                                    false)
                                .node();

                            // $(rowNode).find('td').eq(0).addClass('text-center');
                            // $(rowNode).find('td').eq(4).addClass('text-center text-nowrap');
                        });
                    } else {
                        $('#tableProductSaleTransactionBody').html(tableEmpty(6,
                            'barang kadaluarsa'));
                    }
                }
            });
        };
    </script>
@endpush
