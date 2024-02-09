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
                            Daftar Barang Belanja
                        </div>
                    </div>
                </div>
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
                            <div class="widget-numbers mb-2" id="countProduct">-</div>
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
                            <label for="addProduct" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">Nama
                                / Barcode Barang
                                <span class="required-label">*</span></label>
                            <div class="col-lg-9 col-md-9 col-sm-8">
                                <div class="select2-input select2-info" style="width: 100%">
                                    <select id="addProduct" name="addProduct" class="form-control rounded__10">
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
                        <table class="mb-0 table" id="tableWholesalePurchaseProduct">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Barcode</th>
                                    <th>Nama</th>
                                    <th>Satuan</th>
                                    <th>Harga Pokok</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableWholesalePurchaseProductBody">
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
            $('#tableWholesalePurchaseProduct').DataTable({
                pageLength: 10,
                info: false,
            })

            $('#addProduct').select2({
                theme: "bootstrap-5",
                placeholder: 'Masukkan Nama atau Barcode Barang',
                width: '100%',
                allowClear: true,
                minimumInputLength: 1, // Minimum characters required to start searching
                language: {
                    inputTooShort: function(args) {
                        var remainingChars = args.minimum - args.input.length;
                        return "Masukkan kata kunci setidaknya " + remainingChars + " karakter";
                    },
                    searching: function() {
                        return "Sedang mengambil data...";
                    },
                    noResults: function() {
                        return "Barang tidak ditemukan";
                    },
                    errorLoading: function() {
                        return "Terjadi kesalahan saat memuat data";
                    },
                },
                templateSelection: function(data, container) {
                    if (data.id === '') {
                        return data.text;
                    }
                    var match = data.text.match(/^(.*?) \(/);
                    var resultName = match[1];

                    return $('<span class="custom-selection">' + resultName + '</span>');
                },
                ajax: {
                    url: "{{ route('barang.cari.data') }}", // URL to fetch data from
                    dataType: 'json', // Data type expected from the server
                    processResults: function(response) {
                        var products = response.data.products;
                        var options = [];

                        products.forEach(function(product) {
                            options.push({
                                id: product.IdBarang, // Use the product
                                text: product.nmBarang + ' (' + product.IdBarang +
                                    ')' + ' (' + product.hargaJual +
                                    ')' // menampilkan nama, barcode, dan harga
                            });
                        });

                        return {
                            results: options // Processed results with id and text properties
                        };
                    },
                    cache: true, // Cache the results for better performance
                }
            }).on('change', function(e) {
                // Mendapatkan nilai yang dipilih
                var IdBarang = $(this).val();

                $.ajax({
                    type: "POST",
                    url: "{{ route('wholesale.purchase.store') }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        IdBarang: IdBarang,
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Menambah Produk Berhasil',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        getWholesalePurchaseProduct();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.responseJSON) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: `Tambah Produk Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`,
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }
                        return false;
                    },
                });

                // $('#product').val(null).trigger('change');
            });

            getWholesalePurchaseProduct();
        });

        const getWholesalePurchaseProduct = () => {
            $('#tableWholesalePurchaseProduct').DataTable().clear().draw();
            $('#tableWholesalePurchaseProductBody').html(tableLoader(8,
                `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                url: "{{ route('wholesale.purchase.index.data') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    $('#countProduct').html(response.data.wholesalePurchases.length);
                    if (response.data.wholesalePurchases.length > 0) {
                        response.data.wholesalePurchases.forEach((product, index) => {
                            $('#tableWholesalePurchaseProduct').DataTable().row.add([
                                index + 1,
                                product.IdBarang,
                                product.nmBarang,
                                product.satuan,
                                product.hargaPokok,
                                product.jumlah,
                                product.total,
                                `<button class="btn btn-danger rounded-pill px-3" onclick="deleteWholesalePurchaseProduct('${product.id}','${product.nmBarang}')"><i class="bi bi-trash"></button>`
                            ]).draw(false).node();
                        });
                    } else {
                        $('#tableWholesalePurchaseProductBody').html(tableEmpty(8,
                            'barang'));
                    }
                },
                error: function(error) {
                    $('#tableWholesalePurchaseProductBody').html(tableEmpty(8,
                        'barang'));
                }
            });
        }

        const deleteWholesalePurchaseProduct = (id, name) => {
            Swal.fire({
                title: 'Hapus Produk',
                text: `Apakah Anda yakin ingin menghapus ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: `{{ url('belanja/${id}') }}`,
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Hapus Produk Berhasil',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            getWholesalePurchaseProduct();
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            if (xhr.responseJSON) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: `Hapus Produk Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`,
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                            }
                            return false;
                        },
                    });
                }
            })
        }
    </script>
@endpush
