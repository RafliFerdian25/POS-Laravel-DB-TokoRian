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
                    <div>Pembelian {{ $purchase->id }}
                        <div class="page-title-subheading">
                            Daftar Barang Pembelian
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
                            <div class="widget-heading col-10 widget__title">Total Pembelian</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="sumAmountPurchase">-</span></div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3 widget-content">
                    <div class="content">
                        <div class="widget-content-left mb-2">
                            <i class="pe-7s-cash col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Jumlah Barang Pembelian</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="countProduct">-</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-8 col-xl-9 p-3">
                <div class="main-card mb-3 card">
                    <form id="formAddProductPurchase">
                        <div class="card-body">
                            <h5 class="card-title text-center">Tambah Barang</h5>
                            @csrf
                            <div class="form-group form-show-validation row select2-form-input">
                                <label for="product_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-left">Nama
                                    / Barcode Barang
                                    <span class="required-label">*</span></label>
                                <div class="col-lg-9 col-md-9 col-sm-8">
                                    <div class="select2-input select2-info" style="width: 100%">
                                        <select id="product_id" name="product_id" class="form-control rounded__10">
                                            <option value="">&nbsp;</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-show-validation row select2-form-input">
                                <label for="exp_date" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-left">Tanggal
                                    Kadaluarsa
                                    <span class="required-label">*</span></label>
                                <div class="col-lg-9 col-md-9 col-sm-8">
                                    <input type="date" class="form-control rounded__10" id="exp_date" name="exp_date">
                                </div>
                            </div>
                            <div class="form-group form-show-validation row select2-form-input">
                                <label for="cost_of_good_sold" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-left">Harga
                                    Pokok
                                    <span class="required-label">*</span></label>
                                <div class="col-lg-9 col-md-9 col-sm-8">
                                    <input type="number" class="form-control rounded__10" id="cost_of_good_sold"
                                        name="cost_of_good_sold" min="1">
                                </div>
                            </div>
                            <div class="form-group form-show-validation row select2-form-input">
                                <label for="quantity" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-left">Jumlah
                                    <span class="required-label">*</span></label>
                                <div class="col-lg-9 col-md-9 col-sm-8">
                                    <input type="number" class="form-control rounded__10" id="quantity" name="quantity"
                                        min="1">
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
                                <th>Tanggal Kadaluarsa Lama</th>
                                <th>Tanggal Kadaluarsa Baru</th>
                                <th>Harga Pokok Lama</th>
                                <th>Harga Pokok Baru</th>
                                <th>Total Barang</th>
                                <th>Total Pembelian</th>
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
            $('#tableListProduct').DataTable({
                "scrollX": true,
                "columnDefs": [{
                    "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                    "className": "text-center"
                }],

            })

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
            }).on('select2:select', function(e) {
                var data = e.params.data.data;
                console.log(data);

                $('#exp_date').val(moment(data.expDate, 'YYYY-MM-DD').format('YYYY-MM-DD'));
                $('#cost_of_good_sold').val(data.hargaPokok);
            })

            getProduct();
        });
        var printPriceProduct = [];

        const getProduct = () => {
            $('#tableListProduct').DataTable().clear().draw();
            $('#tableListProductBody').html(tableLoader(8, `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                type: "GET",
                url: `{{ url('pembelian/detail/' . $purchase->id . '/data') }}`,
                data: $('#formFilterProduct').serialize(),
                dataType: "json",
                success: function(response) {
                    $('#sumAmountPurchase').html(response.data.sumAmountPurchase);
                    $('#countProduct').html(response.data.countProduct);
                    if (response.data.purchaseDetails.length > 0) {
                        printPriceProduct = response.data.purchaseDetails.map((purchase) => {
                            return purchase.idBarang;
                        });
                        $.each(response.data.purchaseDetails, function(index, purchaseDetail) {
                            var rowData = [
                                index + 1,
                                purchaseDetail.product_id,
                                purchaseDetail.product.nmBarang,
                                purchaseDetail.exp_date_old != null ? moment(purchaseDetail
                                    .exp_date_old).format('DD-MM-YYYY') : '-',
                                purchaseDetail.exp_date != null ? moment(purchaseDetail
                                    .exp_date).format('DD-MM-YYYY') : '-',
                                purchaseDetail.cost_of_good_sold_old,
                                purchaseDetail.cost_of_good_sold,
                                purchaseDetail.quantity,
                                purchaseDetail.sub_amount,
                                `<button class="btn btn-sm btn-warning" onclick="showEdit('${purchaseDetail.id}')">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteProduct('${purchaseDetail.id}')"><i class="bi bi-trash"></i></button>`
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

        // Menampilkan modal edit product
        const showEdit = (id) => {
            // Mengisi konten modal dengan data yang sesuai
            let modalContent = $('#modalEdit .modal-content');

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
                url: `{{ url('pembelian/detail/${id}/edit') }}`,
                success: function(response) {
                    let formId = `formEditProduct${id}`;

                    modalContent.html(`
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Pembelian Barang</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="${formId}">
                            @method('PUT')
                            @csrf
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <label for="costOfGoodSold" class="col-sm-2 col-form-label">Harga Pokok Baru</label>
                                    <div class="col-sm-10">
                                        <input required value="${response.data.purchaseDetail.cost_of_good_sold}" type="number"
                                            class="form-control rounded__10 "
                                            min="0" id="costOfGoodSold" name="costOfGoodSold">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="qty" class="col-sm-2 col-form-label">Jumlah</label>
                                    <div class="col-sm-10">
                                        <input required value="${response.data.purchaseDetail.quantity}" type="number"
                                            class="form-control rounded__10 "
                                            min="0" id="qty" name="qty">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="expDate" class="col-sm-2 col-form-label">Tanggal Kadaluarsa</label>
                                    <div class="col-sm-10">
                                        <input value="${moment(response.data.purchaseDetail.exp_date).format('YYYY-MM-DD')}" type="date"
                                            class="form-control rounded__10 "
                                            id="expDate" name="expDate">
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
                            costOfGoodSold: {
                                required: true,
                                number: true,
                                min: 0
                            },
                            qty: {
                                required: true,
                                number: true,
                                min: 0
                            },
                            expDate: {
                                date: true,
                            },
                        },
                        messages: {
                            costOfGoodSold: {
                                required: "Harga pokok tidak boleh kosong",
                                number: "Harga pokok harus berupa angka",
                                min: "Harga pokok minimal 0"
                            },
                            qty: {
                                required: "Jumlah tidak boleh kosong",
                                number: "Jumlah harus berupa angka",
                                min: "Jumlah minimal 0"
                            },
                            expDate: {
                                date: "Tanggal kadaluarsa tidak valid",
                            }
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
                            console.log($('#costOfGoodSold').val());
                            event.preventDefault();
                            $('#updateButton').html(
                                '<svg class="spinners-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M12 22c5.421 0 10-4.579 10-10h-2c0 4.337-3.663 8-8 8s-8-3.663-8-8c0-4.336 3.663-8 8-8V2C6.579 2 2 6.58 2 12c0 5.421 4.579 10 10 10z"></path></svg>'
                            );
                            $('#updateButton').prop('disabled', true);
                            $.ajax({
                                url: `{{ url('/pembelian/detail/${id}') }}`,
                                type: "POST",
                                data: {
                                    _method: 'PUT',
                                    _token: '{{ csrf_token() }}',
                                    costOfGoodSold: $('#costOfGoodSold').val(),
                                    qty: $('#qty').val(),
                                    expDate: $('#expDate').val(),
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
                                            getProduct();
                                            // menutup modal
                                            $('#modalEdit').modal('hide');
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
            $('#modalEdit').modal('show');
        }

        $('#formAddProductPurchase').validate({
            rules: {
                product_id: {
                    required: true,
                },
                exp_date: {
                    date: true,
                },
                cost_of_good_sold: {
                    required: true,
                    min: 1,
                },
                quantity: {
                    required: true,
                    min: 1,
                },
            },
            messages: {
                product_id: {
                    required: "Barang tidak boleh kosong",
                },
                exp_date: {
                    date: "Tanggal kadaluarsa tidak valid",
                },
                cost_of_good_sold: {
                    required: "Harga pokok tidak boleh kosong",
                },
                quantity: {
                    required: "Jumlah tidak boleh kosong",
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
                    url: `{{ url('pembelian/detail/' . $purchase->id) }}`,
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: $('#product_id').val(),
                        exp_date: $('#exp_date').val(),
                        cost_of_good_sold: $('#cost_of_good_sold').val(),
                        quantity: $('#quantity').val(),
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
                    $.ajax({
                        type: "DELETE",
                        url: `{{ url('pembelian/detail/${id}') }}`,
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
