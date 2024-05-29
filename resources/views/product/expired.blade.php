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
                    <div>Laporan Barang Kadaluarsa
                        <div class="page-title-subheading">
                            Barang Kadaluarsa
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
                            <div class="widget-heading col-10 widget__title">Total Barang Kadaluarsa</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="countProduct">-</span></div>
                            <div class="perubahan row">
                                {{-- <div class="widget-subheading col-10" id="total_pendapatan">
                                    -2000000
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END CARD DASHBOARD -->
        {{--  --}}
        <!-- FILTER Barang Kadaluarsa -->
        <div class="FilterExpiredProductSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Filter Barang Kadaluarsa</h5>
                    <form id="formFilterProduct" method="GET" onsubmit="event.preventDefault(); getExpiredProduct();">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="filterName" class="col-sm-2 col-form-label">Nama Barang</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control rounded__10 " id="filterName"
                                        name="filterName">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="filterCategory" class="col-sm-2 col-form-label">Kategori</label>
                                <div class="col-sm-10">
                                    <select class="form-select rounded__10 " name="filterCategory" id="filterCategory"
                                        aria-label="Default select example">
                                        <option value="" selected>Pilih Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->ID }}">{{ $category->keterangan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="filterStartDate" class="col-sm-2 col-form-label">Awal Tanggal Kadaluarsa</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control rounded__10 " id="filterStartDate"
                                        name="filterStartDate">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="filterEndDate" class="col-sm-2 col-form-label">Akhir Tanggal Kadaluarsa</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control rounded__10 " id="filterEndDate"
                                        name="filterEndDate">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end barang terjual -->

        <!-- Barang Kadaluarsa -->
        <div class="expiredProductSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Barang Kadaluarsa</h5>
                    <table id="tableExpiredProduct" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center">Barcode</th>
                                <th class="text-center">Nama Barang</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Tanggal Kadaluarsa</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableExpiredProductBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end barang terjual -->
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
            var configDataTable = {
                columnDefs: [{
                    targets: [3], // Kolom "Tanggal" ada di indeks 1 (0-indexed)
                    type: "date-eu" // Tentukan tipe pengurutan khusus untuk format "DD/MM/YYYY",
                }, {
                    "targets": [0, 1, 2, 3, 4],
                    "className": "text-center"
                }],
                order: [
                    [3, 'asc']
                ],
            }
            initializeDataTable('tableExpiredProduct', configDataTable)

            getExpiredProduct();
        });

        const getExpiredProduct = () => {
            var formData = $('#formFilterProduct').serialize();
            // menonaktifkan form ketika proses pengambilan data
            disableFormElements('formFilterProduct', true)

            $('#tableExpiredProduct').DataTable().clear().draw();
            $('#tableExpiredProductBody').html(tableLoader(5, `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                type: "GET",
                url: `{{ route('barang.kadaluarsa.data') }}`,
                data: formData,
                dataType: "json",
                success: function(response) {
                    // mengaktifkan form ketika proses pengambilan data selesai
                    disableFormElements('formFilterProduct', false)
                    $('#countProduct').html(response.data.countProduct);
                    if (response.data.products.length > 0) {
                        $.each(response.data.products, function(index, product) {
                            var rowData = [
                                product.IdBarang,
                                product.nmBarang,
                                product.stok,
                                product.expDate != null && product.expDate != "0000-00-00" ?
                                moment(product.expDate, 'YYYY-MM-DD')
                                .format('DD-MM-YYYY') : "-",
                                `<button class="btn btn-sm btn-warning" onclick="showEdit('${product.IdBarang}')">Edit</button>
                                <button class="btn btn-sm btn-primary" onclick="addProductExpired('${product.IdBarang}','${product.stok}','${product.expDate}')">Tambah Daftar Expired</button>`
                            ];
                            var rowNode = $('#tableExpiredProduct').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();

                            // $(rowNode).find('td').eq(0).addClass('text-center');
                            // $(rowNode).find('td').eq(4).addClass('text-center text-nowrap');
                        });
                    } else {
                        $('#tableExpiredProductBody').html(tableEmpty(5,
                            'barang kadaluarsa'));
                    }
                }
            });
        }

        $('#filterName').on('input', debounce(getExpiredProduct, 750));
        $('#filterCategory').on('change', debounce(getExpiredProduct, 100));
        $('#filterStartDate').on('input', debounce(getExpiredProduct, 750));
        $('#filterEndDate').on('input', debounce(getExpiredProduct, 750));

        function showEdit(idBarang) {
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
                url: `{{ url('/barang/${idBarang}/edit') }}`,
                success: function(response) {
                    let formId = `formEditProduct${idBarang}`;

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
                                    <input required value="${response.product.IdBarang}" type="text"
                                        class="form-control rounded__10 "
                                        id="IdBarang" name="IdBarang" max="999999999999999" pattern="[0-9]*" inputmode="numeric">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="nmBarang" class="col-sm-2 col-form-label">Nama Barang</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.nmBarang}" type="text"
                                        class="form-control rounded__10 "
                                        id="nmBarang" name="nmBarang" style="text-transform:uppercase">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="merk_id" class="col-sm-2 col-form-label">Merk</label>
                                <div class="col-sm-10">
                                    <div class="select2-input select2-info" style="width: 100%">
                                        <select id="merk_id" name="merk_id" class="form-control rounded__10">
                                            <option value="">&nbsp;</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="satuan" class="col-sm-2 col-form-label">Satuan</label>
                                <div class="col-sm-10">
                                    <select required
                                        class="form-select rounded__10"
                                        name="satuan" aria-label="Default select example">
                                        ${response.units.map((unit) => {
                                            return `<option value="${unit.satuan}" ${unit.satuan == response.product.satuan ? "selected" : ""}>${unit.satuan}</option>`
                                        })}
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="isi" class="col-sm-2 col-form-label">Isi</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.isi}" type="number"
                                        class="form-control rounded__10 "
                                        min="0" id="isi" name="isi">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="hargaPokok" class="col-sm-2 col-form-label">Harga Pokok</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.hargaPokok}" type="number"
                                        class="form-control rounded__10 "
                                        min="0" id="hargaPokok" name="hargaPokok">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="hargaJual" class="col-sm-2 col-form-label">Harga Jual</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.hargaJual}" type="number"
                                        class="form-control rounded__10 "
                                        min="0" id="hargaJual" name="hargaJual">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="hargaGrosir" class="col-sm-2 col-form-label">Harga Grosir</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.hargaGrosir}" type="number"
                                        class="form-control rounded__10 "
                                        min="0" id="hargaGrosir" name="hargaGrosir">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="stok" class="col-sm-2 col-form-label">Stok</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.stok}" type="number"
                                        class="form-control rounded__10 "
                                        min="0" id="stok" name="stok">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="expDate" class="col-sm-2 col-form-label">Tanggal Kadaluarsa</label>
                                <div class="col-sm-10">
                                    <input value="${response.product.expDate}" type="date"
                                        class="form-control rounded__10 "
                                        id="expDate" name="expDate">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="jenis" class="col-sm-2 col-form-label">Kategori</label>
                                <div class="col-sm-10">
                                    <select required
                                        class="form-select rounded__10 "
                                        name="jenis" aria-label="Default select example">
                                        ${response.categories.map((category) => {
                                            return `<option value="${category.ID}" ${category.ID == response.product.jenis ? "selected" : ""}>${category.keterangan}</option>`
                                        })}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" id="updateButton" class="btn btn-primary">Ubah</button>
                        </div>
                        </form>
                    `);

                    initSelect2Merk(response.product.merk);

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
                            merk_id: {
                                required: true,
                            },
                            satuan: {
                                required: true,
                            },
                            isi: {
                                required: true,
                                number: true,
                                min: 0
                            },
                            hargaPokok: {
                                required: true,
                                number: true,
                                min: 0
                            },
                            hargaJual: {
                                required: true,
                                number: true,
                                min: 0
                            },
                            hargaGrosir: {
                                required: true,
                                number: true,
                                min: 0
                            },
                            stok: {
                                required: true,
                                number: true,
                                min: 0
                            },
                            jenis: {
                                required: true,
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
                            merk_id: {
                                required: "Merk tidak boleh kosong",
                            },
                            satuan: {
                                required: "Satuan tidak boleh kosong",
                            },
                            isi: {
                                required: "Isi tidak boleh kosong",
                                number: "Isi harus berupa angka",
                                min: "Isi minimal 0"
                            },
                            hargaPokok: {
                                required: "Harga pokok tidak boleh kosong",
                                number: "Harga pokok harus berupa angka",
                                min: "Harga pokok minimal 0"
                            },
                            hargaJual: {
                                required: "Harga jual tidak boleh kosong",
                                number: "Harga jual harus berupa angka",
                                min: "Harga jual minimal 0"
                            },
                            hargaGrosir: {
                                required: "Harga grosir tidak boleh kosong",
                                number: "Harga grosir harus berupa angka",
                                min: "Harga grosir minimal 0"
                            },
                        },
                        errorClass: "invalid-feedback",
                        highlight: function(element) {
                            $(element).closest('.form-control').removeClass('is-valid')
                                .addClass('is-invalid');
                        },
                        unhighlight: function(element) {
                            $(element).closest('.form-control').removeClass('is-invalid');
                        },
                        errorPlacement: function(error, element) {
                            if (element.hasClass('select2-hidden-accessible')) {
                                error.insertAfter(element.next('.select2-container'));
                            } else {
                                error.insertAfter(element);
                            }
                        },
                        submitHandler: function(form, event) {
                            event.preventDefault();
                            var formData = new FormData(form);
                            $('#updateButton').html(inlineLoader());
                            $('#updateButton').prop('disabled', true);
                            $.ajax({
                                url: `{{ url('/barang/${response.product.IdBarang}') }}`,
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    $('#updateButton').html('Ubah');
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
                                            getExpiredProduct();
                                            // menyembunyikan modal
                                            $('#modalEdit').modal('hide');
                                        });
                                },
                                error: function(xhr, status, error) {
                                    $('#updateButton').html('Ubah');
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

        const initSelect2Merk = (merk) => {
            // $('#merk_id').html(`<option value="${id}" selected>${id}</option>`);
            $('#merk_id').select2({
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
                    url: "{{ route('merk.search.data') }}", // URL to fetch data from
                    dataType: 'json', // Data type expected from the server
                    processResults: function(response) {
                        var merks = response.data.merks;
                        var options = [];

                        merks.forEach(function(merk) {
                            options.push({
                                id: merk.id, // Use the merk
                                text: merk.merk + ' (' + merk.keterangan +
                                    ')'
                            });
                        });

                        return {
                            results: options // Processed results with id and text properties
                        };
                    },
                    cache: true, // Cache the results for better performance
                }
            }).on('select2:select', function(e) {
                $(this).removeClass('is-invalid');
            });

            // Periksa apakah nilai id sudah ada dalam opsi saat ini
            if ($('#merk_id').find('option[value="' + merk.id + '"]').length === 0) {
                // Jika tidak, tambahkan elemen <option> baru
                var $newOption = new Option(`${merk.merk} (${merk.keterangan})`, merk.id, true, true);
                $('#merk_id').append($newOption).trigger('change');
            } else {
                // Jika sudah ada, langsung atur nilai dan perbarui tampilan
                $('#merk_id').val(id).trigger('change');
            }
        }


        const addProductExpired = (IdBarang, stock, expiredDate) => {
            var expDate = moment(expiredDate, 'YYYY-MM-DD').format('YYYY-MM-DD');
            Swal.fire({
                title: "Tambah Pembelian Grosir",
                html: `
                <form id="formaddProductExpired">
                    @csrf
                    <div class="">
                        <label for="quantity" class="col-sm-6 col-form-label">Jumlah Barang</label>
                        <div class="">
                            <input required type="number" class="form-control rounded__10 " id="quantity" name="quantity" min="1" value="${stock}">
                        </div>
                    </div>
                    <div class="">
                        <label for="expiredDate" class="col-sm-6 col-form-label">Tanggal Kadaluarsa</label>
                        <div class="">
                            <input required type="date" class="form-control rounded__10 " id="expiredDate" name="expiredDate" value="${expDate}">
                        </div>
                    </div>
                </form>
                `,
                showCancelButton: true,
                confirmButtonText: "Tambah",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-danger"
                },
                customId: {
                    confirmButton: "submitaddProductExpired"
                },
                preConfirm: () => {
                    const quantity = Swal.getPopup().querySelector('#quantity').value;
                    const expiredDate = Swal.getPopup().querySelector('#expiredDate').value;

                    if (!quantity) {
                        Swal.showValidationMessage(`Jumlah Barang tidak boleh kosong`);
                    }

                    return {
                        quantity: quantity,
                        expiredDate: expiredDate
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: `{{ url('/barang-pernah-kadaluarsa/${IdBarang}') }}`,
                        data: {
                            _token: "{{ csrf_token() }}",
                            quantity: result.value.quantity,
                            expiredDate: result.value.expiredDate,
                        },
                        success: function(response) {
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
                                    getExpiredProduct()
                                });
                        },
                        error: function(xhr, status, error) {
                            if (xhr.responseJSON) {
                                errorAlert("Gagal!",
                                    `Tambah Belanja Barang Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`
                                );
                            } else {
                                errorAlert("Gagal!",
                                    `Terjadi kesalahan pada server. Error: ${xhr.responseText}`
                                );
                            }
                            return false;
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Batal",
                        text: "Tambah Belanja Barang dibatalkan",
                        icon: "info",
                        showCancelButton: false,
                        confirmButtonText: "Okay",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        },
                    });
                }
            })
        }
    </script>
@endpush
