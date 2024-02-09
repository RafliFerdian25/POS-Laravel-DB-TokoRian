@extends('layouts.main')

@section('content')
    {{-- @dd($products[0]->id) --}}
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- Barang section -->
        <!-- TITLE -->
        <div class="app-page-title row justify-content-lg-between">
            <div class="page-title-wrapper col-3">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-drawer icon-gradient bg-plum-plate">
                        </i>
                    </div>
                    <div>Belanja
                        <div class="page-title-subheading">
                            Dashboard
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 text-center align-self-center">
                <a href="{{ url('/belanja/create') }}">
                    <button class="btn btn-primary rounded-pill px-3" id="tambah-produk">Tambah</button>
                </a>
            </div>
        </div>
        <!-- END TITLE -->

        <!-- CARD DASHBOARD -->
        <div class="row">
            <!-- total pendapatan -->
            <div class="col-sm-12 col-md-4 col-xl-3 p-3">
                <div class="card mb-3 widget-content">
                    <div class="content">
                        <div class="widget-content-left mb-2">
                            <i class="pe-7s-cash col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Barang Cetak Harga</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="countProduct">-</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-8 col-xl-9 p-3">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Tambah Barang</h5>
                        @csrf
                        <div class="form-group form-show-validation row select2-form-input">
                            <label for="product" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">Nama
                                / Barcode Barang
                                <span class="required-label">*</span></label>
                            <div class="col-lg-9 col-md-9 col-sm-8">
                                <div class="select2-input select2-info" style="width: 100%">
                                    <select id="product" name="product" class="form-control rounded__10">
                                        <option value="">&nbsp;</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="qr-reader" style="min-width:300px"></div>
                        <div id="qr-reader-results"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END CARD DASHBOARD -->

        <div class="belanja__section">
            <!-- Barang -->
            <div class="belanja__content">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title text-center font-size-xlg">Belanja</h5>
                        <table class="mb-0 table" id="tableWholesalePurchase">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Barcode</th>
                                    <th>Nama</th>
                                    <th>Satuan</th>
                                    <th>Harga Pokok</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableWholesalePurchaseBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end Barang section -->
    </div>
    <!-- END Section layouts   -->
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#tableWholesalePurchase').DataTable({
                pageLength: 10,
                info: false,
            })
            getWholesalePurchase();
        });

        const getWholesalePurchase = () => {
            $('#tableWholesalePurchase').DataTable().clear().draw();
            $('#tableWholesalePurchaseBody').html(tableLoader(5,
                `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                url: "{{ route('wholesale.purchase.index.data') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    $('#countProduct').html(response.data.countProduct);
                    if (response.data.wholesalePurchases.length > 0) {
                        response.data.wholesalePurchases.forEach((product, index) => {
                            $('#tableWholesalePurchase').DataTable().row.add([
                                index + 1,
                                product.IdBarang,
                                product.nmBarang,
                                product.satuan,
                                product.hargaPokok,
                                product.jumlah,
                                `<button class="btn btn-danger rounded-pill px-3"><i class="bi bi-trash"></button>`
                            ]).draw(false).node();
                        });
                    } else {
                        $('#tableWholesalePurchaseBody').html(tableEmpty(5,
                            'barang'));;
                    }
                },
                error: function(error) {
                    $('#tableWholesalePurchaseBody').html(tableEmpty(5,
                        'barang'));
                }
            });
        }
    </script>
@endpush
