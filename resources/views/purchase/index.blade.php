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
                                    <th>Stok</th>
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
                responsive: true,

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
                                product.product.stok,
                                product.satuan,
                                product.hargaPokok,
                                product.jumlah,
                                product.total,
                                `<button class="btn btn-danger rounded-circle px-2" onclick="deleteWholesalePurchaseProduct('${product.id}','${product.nmBarang}')"><i class="bi bi-trash"></i></button>
                                    <button class="btn btn-primary rounded-circle px-2" onclick="editWholesalePurchaseProduct('${product.id}')"><i class="bi bi-pencil"></i></button>`
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

        // Menampilkan modal edit product
        const editWholesalePurchaseProduct = (id) => {
            // Mengisi konten modal dengan data yang sesuai
            let modalContent = $('#modalMain .modal-content');

            modalContent.html(`
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex justify-content-center align-items-center">
                    <svg class="loader" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="25" height="25">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="#3498db" stroke-width="5" stroke-dasharray="89 89" stroke-linecap="round">
                            <animateTransform attributeName="transform" dur="1s" type="rotate" from="0 50 50" to="360 50 50" repeatCount="indefinite" />
                        </circle>
                    </svg>
                </div>
            `);
            // mengirim request ajax
            $.ajax({
                type: "GET",
                url: `{{ url('/belanja/${id}/edit') }}`,
                success: function(response) {
                    let formId = `formEditProduct${id}`;

                    modalContent.html(`
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="${formId}">
                            @method('PUT')
                            @csrf
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <label for="IdBarang" class="col-sm-2 col-form-label">Kode Barang</label>
                                    <div class="col-sm-10">
                                        <input required value="${response.data.wholesalePurchaseProduct.IdBarang}" type="text"
                                            class="form-control rounded__10" disabled
                                            id="IdBarang" name="IdBarang" max="999999999999999" pattern="[0-9]*" inputmode="numeric">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="nmBarang" class="col-sm-2 col-form-label">Nama Barang</label>
                                    <div class="col-sm-10">
                                        <input required value="${response.data.wholesalePurchaseProduct.nmBarang}" type="text"
                                            class="form-control rounded__10 " disabled
                                            id="nmBarang" name="nmBarang" style="text-transform:uppercase">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="satuan" class="col-sm-2 col-form-label">Satuan</label>
                                    <div class="col-sm-10">
                                        <select required disabled
                                            class="form-select rounded__10"
                                            name="satuan" id="satuan" aria-label="Default select example">
                                            ${response.data.units.map((unit) => {
                                                return `<option value="${unit.satuan}" ${unit.satuan == response.data.wholesalePurchaseProduct.satuan ? "selected" : ""}>${unit.satuan}</option>`
                                            })}
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="stok" class="col-sm-2 col-form-label">Stok</label>
                                    <div class="col-sm-10">
                                        <input required value="${response.data.wholesalePurchaseProduct.product.stok}" type="number"
                                            class="form-control rounded__10 "
                                            min="0" id="stok" name="stok">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="hargaPokok" class="col-sm-2 col-form-label">Harga Pokok</label>
                                    <div class="col-sm-10">
                                        <input required value="${response.data.wholesalePurchaseProduct.hargaPokok}" type="number"
                                            class="form-control rounded__10 "
                                            min="0" id="hargaPokok" name="hargaPokok">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="jumlah" class="col-sm-2 col-form-label">Jumlah</label>
                                    <div class="col-sm-10">
                                        <input required value="${response.data.wholesalePurchaseProduct.jumlah}" type="number"
                                            class="form-control rounded__10 "
                                            min="0" id="jumlah" name="jumlah">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" id="updateButton" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    `);

                    // submit form
                    $(`#${formId}`).validate({
                        rules: {
                            IdBarang: {
                                required: true,
                                maxlength: 15,
                                minlength: 3,
                                number: true
                            },
                            nmBarang: {
                                required: true,
                                maxlength: 50,
                                minlength: 3,
                            },
                            satuan: {
                                required: true,
                            },
                            stok: {
                                required: true,
                                number: true,
                                min: 0
                            },
                            hargaPokok: {
                                required: true,
                                number: true,
                                min: 0
                            },
                            jumlah: {
                                required: true,
                                number: true,
                                min: 0
                            },
                        },
                        messages: {
                            IdBarang: {
                                required: "Kode barang tidak boleh kosong",
                                maxlength: "Kode barang maksimal 15 karakter",
                                minlength: "Kode barang minimal 3 karakter",
                                number: "Kode barang harus berupa angka"
                            },
                            nmBarang: {
                                required: "Nama barang tidak boleh kosong",
                                maxlength: "Nama barang maksimal 50 karakter",
                                minlength: "Nama barang minimal 3 karakter",
                            },
                            satuan: {
                                required: "Satuan tidak boleh kosong",
                            },
                            stok: {
                                required: "Stok tidak boleh kosong",
                                number: "Stok harus berupa angka",
                                min: "Stok minimal 0"
                            },
                            hargaPokok: {
                                required: "Harga pokok tidak boleh kosong",
                                number: "Harga pokok harus berupa angka",
                                min: "Harga pokok minimal 0"
                            },
                        },
                        highlight: function(element) {
                            $(element).closest('.form-group').removeClass('has-success')
                                .addClass('has-error');
                        },
                        success: function(element) {
                            $(element).closest('.form-group').removeClass('has-error');
                        },
                        submitHandler: function(form, event) {
                            event.preventDefault();
                            $('#updateButton').html(
                                '<svg class="spinners-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M12 22c5.421 0 10-4.579 10-10h-2c0 4.337-3.663 8-8 8s-8-3.663-8-8c0-4.336 3.663-8 8-8V2C6.579 2 2 6.58 2 12c0 5.421 4.579 10 10 10z"></path></svg>'
                            );
                            $('#updateButton').prop('disabled', true);
                            $.ajax({
                                url: `{{ url('/belanja/${response.data.wholesalePurchaseProduct.id}') }}`,
                                type: "POST",
                                data: {
                                    _method: 'PUT',
                                    _token: '{{ csrf_token() }}',
                                    IdBarang: $('#IdBarang').val(),
                                    nmBarang: $('#nmBarang').val(),
                                    satuan: $('#satuan').val(),
                                    stok: $('#stok').val(),
                                    hargaPokok: $('#hargaPokok').val(),
                                    jumlah: $('#jumlah').val(),
                                },
                                success: function(response) {
                                    $('#updateButton').html('Update');
                                    $('#updateButton').prop('disabled', false);
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
                                            getWholesalePurchaseProduct();
                                            // menutup modal
                                            $('#modalMain').modal('hide');
                                        });
                                },
                                error: function(xhr, status, error) {
                                    $('#updateButton').html('Update');
                                    $('#updateButton').prop('disabled', false);
                                    if (xhr.responseJSON) {
                                        errorAlert("Gagal!",
                                            `Ubah Mesin Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`
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
                }
            });

            // Menampilkan modal
            $('#modalMain').modal('show');
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
