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
                    <div>Pembelian
                        <div class="page-title-subheading">
                            Daftar Pembelian Barang
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 text-center align-self-center">
                <a href="{{ url('/pembelian/create') }}">
                    <button class="btn btn-primary rounded-pill px-3" id="addProduct">Tambah</button>
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
                            <div class="widget-heading col-10 widget__title">Total Transaksi Pembelian</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2" id="countPurchase">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END CARD DASHBOARD -->

        <div class="pembelian__section">
            <!-- Barang -->
            <div class="pembelian__content">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title text-center font-size-xlg">Belanja</h5>
                        <table class="mb-0 table" id="tablePurchase">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID Transaksi</th>
                                    <th>supplier</th>
                                    <th>Total Barang</th>
                                    <th>Total Pembelian</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tablePurchaseBody">
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
    <script>
        $(document).ready(function() {
            $('#tablePurchase').DataTable({
                pageLength: 10,
                info: false,
                responsive: true,

            })

            getPurchase();
        });

        const getPurchase = () => {
            $('#tablePurchase').DataTable().clear().draw();
            $('#tablePurchaseBody').html(tableLoader(7,
                `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                url: "{{ route('purchase.index.data') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    $('#countPurchase').html(response.data.purchases.length);
                    if (response.data.purchases.length > 0) {
                        response.data.purchases.forEach((purchase, index) => {
                            $('#tablePurchase').DataTable().row.add([
                                index + 1,
                                purchase.id,
                                purchase.supplier.nama,
                                purchase.total,
                                purchase.amount,
                                purchase.created_at,
                                `<button class="btn btn-danger rounded-circle px-2" onclick="deletePurchase('${purchase.id}')"><i class="bi bi-trash"></i></button>
                                    <button class="btn btn-primary rounded-circle px-2" onclick="editPurchase('${purchase.id}')"><i class="bi bi-pencil"></i></button>`
                            ]).draw(false).node();
                        });
                    } else {
                        $('#tablePurchaseBody').html(tableEmpty(7,
                            'barang'));
                    }
                },
                error: function(error) {
                    $('#tablePurchaseBody').html(tableEmpty(7,
                        'barang'));
                }
            });
        }

        // Menampilkan modal edit product
        const editPurchase = (id) => {
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
                url: `{{ url('/pembelian/${id}/edit') }}`,
                success: function(response) {
                    let formId = `formEditPurchase${id}`;

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
                                    <label for="supplier_id" class="col-sm-2 col-form-label">Supplier</label>
                                    <div class="col-sm-10">
                                        <div class="select2-input select2-info" style="width: 100%">
                                            <select id="supplier_id" name="supplier_id" class="form-control rounded__10">
                                                <option value="">&nbsp;</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="total" class="col-sm-2 col-form-label">Stok</label>
                                    <div class="col-sm-10">
                                        <input required disabled value="${response.data.purchase.total}" type="number"
                                            class="form-control rounded__10 "
                                            min="0" id="total" name="total">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="amount" class="col-sm-2 col-form-label">Harga Pokok</label>
                                    <div class="col-sm-10">
                                        <input required disabled value="${response.data.purchase.amount}" type="number"
                                            class="form-control rounded__10 "
                                            min="0" id="amount" name="amount">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="created_at" class="col-sm-2 col-form-label">Jumlah</label>
                                    <div class="col-sm-10">
                                        <input required value="${response.data.purchase.created_at}" type="date"
                                            class="form-control rounded__10" max="{{ date('Y-m-d') }}"
                                            id="created_at" name="created_at">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" id="updateButton" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    `);

                    initSelect2Supplier(response.purchase.supplier_id);

                    // submit form
                    $(`#${formId}`).validate({
                        rules: {
                            supplier_id: {
                                required: true,
                            },
                            total: {
                                required: true,
                            },
                            amount: {
                                required: true,
                            },
                            created_at: {
                                required: true,
                            },
                        },
                        messages: {
                            supplier_id: {
                                required: "Supplier tidak boleh kosong",
                            },
                            total: {
                                required: "Stok tidak boleh kosong",
                            },
                            amount: {
                                required: "Harga Pokok tidak boleh kosong",
                            },
                            created_at: {
                                required: "Tanggal tidak boleh kosong",
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
                            $('#updateButton').html(
                                '<svg class="spinners-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M12 22c5.421 0 10-4.579 10-10h-2c0 4.337-3.663 8-8 8s-8-3.663-8-8c0-4.336 3.663-8 8-8V2C6.579 2 2 6.58 2 12c0 5.421 4.579 10 10 10z"></path></svg>'
                            );
                            $('#updateButton').prop('disabled', true);
                            $.ajax({
                                url: `{{ url('/pembelian/${response.data.purchase.id}') }}`,
                                type: "POST",
                                data: {
                                    _method: 'PUT',
                                    _token: '{{ csrf_token() }}',
                                    supplier_id: $('#supplier_id').val(),
                                    total: $('#total').val(),
                                    amount: $('#amount').val(),
                                    created_at: $('#created_at').val(),
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
                                            getPurchase();
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

        const initSelect2Supplier = (supplier) => {
            $('#supplier_id').select2({
                dropdownParent: $('#modalEdit'),
                theme: "bootstrap-5",
                placeholder: 'Masukkan Merk Barang',
                width: '100%',
                allowClear: true,
                minimumInputLength: 1, // Minimum characters required to start searching
                language: {
                    inputTooShort: function(args) {
                        var remainingChars = args.minimum - args.input.length;
                        return "Masukkan kata kunci setidaknya " + remainingChars +
                            " karakter";
                    },
                    searching: function() {
                        return "Sedang mengambil data...";
                    },
                    noResults: function() {
                        return "Merk tidak ditemukan";
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
                    url: "{{ route('supplier.search.data') }}", // URL to fetch data from
                    dataType: 'json', // Data type expected from the server
                    processResults: function(response) {
                        var suppliers = response.data.suppliers;
                        var options = [];

                        suppliers.forEach(function(supplier) {
                            options.push({
                                id: supplier.idSupplier, // Use the supplier
                                text: supplier.Nama + ' (' + supplier.Produk +
                                    ')'
                            });
                        });

                        return {
                            results: options // Processed results with id and text properties
                        };
                    },
                    cache: true, // Cache the results for better performance
                }
            })
            // Periksa apakah nilai id sudah ada dalam opsi saat ini
            if ($('#supplier_id').find('option[value="' + supplier.id + '"]').length === 0) {
                // Jika tidak, tambahkan elemen <option> baru
                var $newOption = new Option(`${supplier.Nama} (${supplier.Produk})`, supplier.id, true, true);
                $('#supplier_id').append($newOption).trigger('change');
            } else {
                // Jika sudah ada, langsung atur nilai dan perbarui tampilan
                $('#supplier_id').val(id).trigger('change');
            }
        }

        const deletePurchase = (id, name) => {
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
                        url: `{{ url('pembelian/${id}') }}`,
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
                            getPurchase();
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
