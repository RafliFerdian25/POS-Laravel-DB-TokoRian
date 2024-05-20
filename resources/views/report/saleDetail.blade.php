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
                    <div>Laporan Detail Penjualan <span class="fw-bold">{{ $sale->noTransaksi }}</span> <span
                            id="typeReportTitleHeading"></span>
                        <div class="page-title-subheading">
                            Laporan penjualan <span class="typeReportTitleSubHeading"></span>
                            <span class="fw-bold" id="dateString"></span>
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

        <!-- Penjualan Barang  -->
        <div class="productSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Penjualan Barang <span id="filterProductDate"></span></h5>
                    <table class="display nowrap" style="width:100%" id="tableProduct">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Barcode</th>
                                <th class="text-center">Nama Barang</th>
                                <th class="text-center">Harga Jual</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Keuntungan</th>
                                {{-- <th>Diskon</th> --}}
                            </tr>
                        </thead>
                        <tbody id="tableProductBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end Penjualan barang -->

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var configDataTable = {
                "columnDefs": [{
                    "targets": [0, 1, 2, 4],
                    "className": "text-center"
                }, {
                    // Mengatur aturan pengurutan kustom untuk kolom keempat (index 3)
                    "targets": [3, 5, 6],
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
            initializeDataTable("tableProduct", configDataTable)

            getReportCategory()
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
            $('#tableProduct').DataTable().clear().draw();
            $('#tableProductBody').html(tableLoader(7, `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                type: "GET",
                url: `{{ url('laporan/penjualan/detail/data') }}`,
                data: {
                    id: '{{ $sale->noTransaksi }}',
                },
                success: function(response) {
                    $('#income').text(formatCurrency(response.data.report.income));
                    $('#profit').text(formatCurrency(response.data.report.profit));
                    $('#total_transaction').text(response.data.report.total_transaction);
                    $('#total_product').text(response.data.report.total_product);

                    $('#filterProductDate').html(response.data.dateString);
                    if (response.data.transactions.length > 0) {
                        $.each(response.data.transactions, function(index, transaction) {
                            var rowData = [
                                index + 1,
                                transaction.idBarang,
                                transaction.nmBarang,
                                transaction.harga,
                                transaction.jumlah,
                                transaction.total,
                                transaction.Laba,
                            ];
                            var rowNode = $('#tableProduct').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();
                        });
                    } else {
                        $('#tableProductBody').html(tableEmpty(7,
                            'barang'));
                    }
                }
            });
        };
    </script>
@endpush
