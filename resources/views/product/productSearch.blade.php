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
                    <div>Barang Dicari
                        <div class="page-title-subheading">
                            Daftar Barang Dicari
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
                            <div class="widget-heading col-10 widget__title">Jumlah Barang Dicari</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="countProduct">-</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-8 col-xl-9 p-3">
                <div class="main-card mb-3 card">
                    <form id="formAddProductSearch">
                        <div class="card-body">
                            <h5 class="card-title text-center">Tambah Barang Dicari</h5>
                            @csrf
                            <p>Pilih salah satu</p>
                            <div class="form-group form-show-validation row select2-form-input">
                                <label for="product_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-left">Barcode
                                    Barang
                                </label>
                                <div class="col-lg-9 col-md-9 col-sm-8">
                                    <div class="select2-input select2-info" style="width: 100%">
                                        <select id="product_id" name="product_id" class="form-control rounded__10">
                                            <option value="">&nbsp;</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-show-validation row select2-form-input">
                                <label for="nameProduct" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-left">Nama Barang
                                </label>
                                <div class="col-lg-9 col-md-9 col-sm-8">
                                    <input type="text" class="form-control rounded__10" id="nameProduct"
                                        name="nameProduct">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer justify-content-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-3 text-right"
                                id="submitButton">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- END CARD DASHBOARD -->

        <!-- Daftar Barang -->
        <div class="ListProductSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Daftar Barang</h5>
                    <table class="display nowrap" style="width:100%" id="tableListProduct">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th>Jumlah Dicari</th>
                                <th>Tanggal Terakhir Dicari</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableListProductBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end Daftar Barang -->
    </div>
@endsection

@section('modal')
    {{-- Modal --}}
    <div class="modal fade modalEdit" id="modalEdit" role="dialog" tabindex="-1" aria-labelledby="modalEditLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    {{-- End modal --}}
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
            var config = {
                "columnDefs": [{
                    "targets": [0, 1, 2, 3],
                    "className": "text-center"
                }],
            }
            initializeDataTable('tableListProduct', config)

            $('#product_id').select2({
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
                                    ')', // menampilkan nama, barcode, dan harga
                                data: product,
                            });
                        });

                        return {
                            results: options // Processed results with id and text properties
                        };
                    },
                    cache: true, // Cache the results for better performance
                }
            })

            getProduct();
        });
        var printPriceProduct = [];

        const getProduct = () => {
            $('#tableListProduct').DataTable().clear().draw();
            $('#tableListProductBody').html(tableLoader(8, `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                type: "GET",
                url: `{{ route('product.search.index.data') }}`,
                dataType: "json",
                success: function(response) {
                    $('#countProduct').html(response.data.countProduct);
                    if (response.data.products.length > 0) {
                        $.each(response.data.products, function(index, product) {
                            var rowData = [
                                index + 1,
                                product.product_id,
                                product.name,
                                product.total,
                                moment(product.created_at).format('DD-MM-YYYY'),
                                `<button class="btn btn-sm btn-danger" onclick="deleteProduct('${product.product_id}')"><i class="bi bi-trash"></i></button>`
                            ];
                            var rowNode = $('#tableListProduct').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();

                            // $(rowNode).find('td').eq(0).addClass('text-center');
                            // $(rowNode).find('td').eq(4).addClass('text-center text-nowrap');
                        });
                    } else {
                        $('#tableListProductBody').html(tableEmpty(8,
                            'barang'));
                    }
                }
            });

        }

        $('#formAddProductSearch').validate({
            rules: {
                product_id: {
                    required: function() {
                        return $('#nameProduct').val() == '';
                    },
                },
                nameProduct: {
                    required: function() {
                        return $('#product_id').val() == '';
                    },
                },
            },
            messages: {
                product_id: {
                    required: "Barcode Barang tidak boleh kosong",
                },
                nameProduct: {
                    required: "Nama Barang tidak boleh kosong",
                },
            },
            errorClass: "invalid-feedback",
            highlight: function(element) {
                $(element).closest('.form-control').removeClass('valid')
                    .addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).closest('.form-control').removeClass('is-invalid');
            },
            success: function(element) {
                $(element).closest('.form-control').removeClass('is-invalid');
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                $('#submitButton').html(
                    '<svg class="spinners-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M12 22c5.421 0 10-4.579 10-10h-2c0 4.337-3.663 8-8 8s-8-3.663-8-8c0-4.336 3.663-8 8-8V2C6.579 2 2 6.58 2 12c0 5.421 4.579 10 10 10z"></path></svg>'
                );
                $('#submitButton').prop('disabled', true);
                $.ajax({
                    url: `{{ route('product.search.store') }}`,
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: $('#product_id').val(),
                        name: $('#nameProduct').val(),
                    },
                    success: function(response) {
                        $('#submitButton').html('Tambah');
                        $('#submitButton').prop('disabled', false);
                        Swal.fire({
                                title: "Berhasil!",
                                text: response.meta.message,
                                icon: "success",
                                showCancelButton: false,
                                confirmButtonText: "Okay",
                                customClass: {
                                    confirmButton: "btn btn-success"
                                },
                            })
                            .then(() => {
                                getProduct();
                            });
                    },
                    error: function(xhr, status, error) {
                        $('#submitButton').html('Tambah');
                        $('#submitButton').prop('disabled', false);
                        if (xhr.responseJSON) {
                            errorAlert("Gagal!",
                                `Tambah Produk Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`
                            );
                        } else {
                            errorAlert("Gagal!",
                                `Terjadi kesalahan pada server. Error: ${xhr.responseText}`
                            );
                        }
                        return false;
                    }
                });
            }
        })

        const deleteProduct = (id) => {
            Swal.fire({
                title: 'Hapus Produk',
                text: `Apakah Anda yakin ingin menghapus produk?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Hapus Produk',
                        text: 'Sedang menghapus produk...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    $.ajax({
                        type: "DELETE",
                        url: `{{ url('barang-dicari/${id}') }}`,
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
                            getProduct();
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
