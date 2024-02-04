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
                    <div>Barang
                        <div class="page-title-subheading">
                            Cetak Harga Jual
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

        <!-- Daftar Barang -->
        <div class="ListProductSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Daftar Barang</h5>
                    <table class="mb-0 table" id="tableListProduct">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th>Harga Jual</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableListProductBody">
                        </tbody>
                    </table>
                </div>
                <div class="card-action pb-3 px-4">
                    {{-- hapus --}}
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-danger ml-3" type="button" id="deleteAllProductButton"
                                onclick="deleteProduct('all')">Hapus Semua</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end Daftar Barang -->
    </div>
@endsection

@push('scripts')
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 1500
            })
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $("#tableListProduct").DataTable({
                pageLength: 10,
                info: false,
            });

            $('#product').select2({
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
                    url: "{{ route('barang.cetak-harga.store') }}",
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
                        getListProduct();
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

            getListProduct();
        });

        const getListProduct = () => {
            $('#tableListProduct').DataTable().clear().draw();
            $('#tableListProductBody').html(tableLoader(5, `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                type: "GET",
                url: `{{ route('barang.cetak-harga.data') }}`,
                data: $('#formFilterProduct').serialize(),
                dataType: "json",
                success: function(response) {
                    $('#countProduct').html(response.data.countProduct);
                    if (response.data.products.length > 0) {
                        $.each(response.data.products, function(index, product) {
                            var rowData = [
                                index + 1,
                                product.idBarang,
                                product.nmBarang,
                                product.hargaJual,
                                `<button class="btn btn-sm btn-danger" onclick="deleteProduct('${product.idBarang}')"><i class="bi bi-trash"></i></button>`
                            ];
                            var rowNode = $('#tableListProduct').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();

                            // $(rowNode).find('td').eq(0).addClass('text-center');
                            // $(rowNode).find('td').eq(4).addClass('text-center text-nowrap');
                        });
                    } else {
                        $('#tableListProductBody').html(tableEmpty(5,
                            'barang'));
                    }
                }
            });
        }

        const deleteProduct = (id) => {
            Swal.fire({
                title: 'Hapus Produk',
                text: `Apakah Anda yakin ingin menghapus ${id == 'all' ? 'semua':''} produk?`,
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
                        url: `{{ url('barang/cetak-harga/${id}') }}`,
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
                            getListProduct();
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

        var resultContainer = document.getElementById('qr-reader-results');
        var lastResult, countResults = 0;

        function onScanSuccess(decodedText, decodedResult) {
            if (decodedText !== lastResult) {
                ++countResults;
                lastResult = decodedText;
                // Handle on success condition with the decoded message.
                $.ajax({
                    type: "POST",
                    url: "{{ route('barang.cetak-harga.store') }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        IdBarang: decodedText,
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Menambah Produk Berhasil',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        getListProduct();
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
            }
        }

        var html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", {
                fps: 10,
                qrbox: {
                    width: 400,
                    height: 250
                },
                rememberLastUsedCamera: true,
            });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
@endpush
