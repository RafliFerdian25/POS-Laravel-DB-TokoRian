@extends('layouts.main')

@section('content')
    <div class="app-main__inner">
        <!-- TITLE -->
        <div class="app-page-title row justify-content-lg-between">
            <div class="page-title-wrapper col-3">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-note2 icon-gradient bg-plum-plate">
                        </i>
                    </div>
                    <div>Daftar Barang
                        <div class="page-title-subheading">
                            Barang
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 text-center align-self-center">
                <a href="{{ url('/barang/create') }}">
                    <button class="btn btn-primary rounded-pill px-3" id="addProduct">Tambah</button>
                </a>
            </div>
        </div>
        <!-- END TITLE -->
        <!-- CARD DASHBOARD -->
        <div class="row">
            <!-- total barang aktif -->
            <div class="col-sm-6 col-md-4 col-xl-3 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-cash col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Barang Aktif</div>
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
            <!-- end total barang aktif -->
            <!-- jumlah stok barang -->
            <div class="col-sm-6 col-md-4 col-xl-3 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-cash col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Jumlah Stok Barang</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="countStockProduct">-</span></div>
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
        <!-- FILTER Barang -->
        <div class="FilterProductSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Filter Barang</h5>
                    <form id="formFilterProduct" method="GET" onsubmit="event.preventDefault(); getProducts();">
                        @csrf
                        <div class="modal-body row">
                            <div class="col-md-6">
                                <div class="row mb-3">
                                    <label for="filterProduct" class="col-sm-2 col-form-label">Nama /
                                        Barcode</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control rounded__10 " id="filterProduct"
                                            name="filterProduct">
                                    </div>
                                </div>
                                <div class="row
                                            mb-3">
                                    <label for="filterCategory" class="col-sm-2 col-form-label">Kategori</label>
                                    <div class="col-sm-10">
                                        <select class="form-select rounded__10 " name="filterCategory" id="filterCategory"
                                            aria-label="Default select example" onchange="getProducts();">
                                            <option value="" selected>Pilih Kategori</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->ID }}">{{ $category->keterangan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="qr-reader" height="250px"></div>
                                <div id="qr-reader-results"></div>
                            </div>
                        </div>
                        {{-- <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div> --}}
                    </form>
                </div>
            </div>
        </div>
        <!-- end barang terjual -->

        <!-- Barang -->
        <div class="productSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Barang</h5>
                    <table class="display nowrap" style="width:100%" id="tableProduct">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Harga Pokok</th>
                                <th>Harga Jual</th>
                                <th>Harga Grosir</th>
                                <th>Stok</th>
                                <th>Tanggal Kadaluarsa</th>
                                <th>Jenis</th>
                                <th>Merk</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableProductBody">
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
            // datatable reponsive
            var configDataTable = {
                "columnDefs": [{
                    "targets": "_all",
                    "className": "text-center"
                }, {
                    // Mengatur aturan pengurutan kustom untuk kolom keempat (index 3)
                    "targets": [4, 5, 6],
                    "render": function(data, type, row) {
                        // Memeriksa tipe data, jika tampilan atau filter
                        if (type === 'display' || type === 'filter') {
                            // Memformat angka menggunakan fungsi formatCurrency
                            return formatCurrency(data);
                        }
                        // Jika tipe data selain tampilan atau filter, kembalikan data tanpa perubahan
                        return data;
                    }
                }, {
                    targets: [8], // Kolom "Tanggal" ada di indeks 1 (0-indexed)
                    type: "date-eu" // Tentukan tipe pengurutan khusus untuk format "DD/MM/YYYY"
                }],
            }
            initializeDataTable("tableProduct", configDataTable);

            // Do this before you initialize any of your modals
            getProducts();
        });

        const getProducts = () => {
            $('#tableProduct').DataTable().clear().draw();
            $('#tableProductBody').html(tableLoader(12));

            $.ajax({
                type: "GET",
                url: `{{ route('barang.data') }}`,
                data: $('#formFilterProduct').serialize(),
                dataType: "json",
                success: function(response) {
                    $('#countProduct').html(response.data.countProduct.count);
                    $('#countStockProduct').html(response.data.countProduct.totalStock);
                    if (response.data.products.length > 0) {
                        $.each(response.data.products, function(index, product) {
                            var rowData = [
                                index + 1,
                                product.IdBarang,
                                product.nmBarang,
                                product.satuan,
                                product.hargaPokok,
                                product.hargaJual,
                                product.hargaGrosir,
                                product.stok,
                                product.expDate != null ? moment(product.expDate, 'YYYY-MM-DD')
                                .format(
                                    'DD-MM-YYYY') : '-',
                                product.jenis,
                                product.merk.merk,
                                `<a href="{{ url('/laporan/barang/${product.IdBarang}') }}" class="btn btn-sm btn-primary">Laporan</a>
                                <button class="btn btn-sm btn-warning" onclick="showEdit('${product.IdBarang}')">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteProduct('${product.IdBarang}', '${product.nmBarang}')"><i class="bi bi-trash"></i></button>`
                            ];
                            var rowNode = $('#tableProduct').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();

                            $(rowNode).find('td').eq(8).addClass('text-center');
                            // $(rowNode).find('td').eq(4).addClass('text-center text-nowrap');
                        });
                    } else {
                        $('#tableProductBody').html(tableEmpty(12,
                            'barang'));
                    }
                }
            });
        }

        $('#filterProduct').on('input', debounce(getProducts, 750));


        function showEdit(idBarang, status) {
            // Mengisi konten modal dengan data yang sesuai
            let modalContent = $('#modalEdit .modal-content');

            modalContent.html(`
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Data Barang</h1>
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
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Data Barang</h1>
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
                                        class="form-control rounded__10"
                                        min="0" step="500" id="hargaJual" name="hargaJual">
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
                                <label for="expDate" class="col-sm-2 col-form-label">Tanggal</label>
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
                        highlight: function(element) {
                            $(element).closest('.form-group').removeClass('has-success').addClass(
                                'has-error');
                        },
                        success: function(element) {
                            $(element).closest('.form-group').removeClass('has-error');
                        },
                        submitHandler: function(form, event) {
                            event.preventDefault();
                            var formData = new FormData(form);
                            $('#updateButton').html(
                                '<svg class="spinners-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M12 22c5.421 0 10-4.579 10-10h-2c0 4.337-3.663 8-8 8s-8-3.663-8-8c0-4.336 3.663-8 8-8V2C6.579 2 2 6.58 2 12c0 5.421 4.579 10 10 10z"></path></svg>'
                            );
                            $('#updateButton').prop('disabled', true);
                            $.ajax({
                                url: `{{ url('/barang/${response.product.IdBarang}') }}`,
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
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
                                            getProducts();
                                            // menyembunyikan modal
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

        const initSelect2Merk = (merk) => {
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
            })
            // Periksa apakah nilai id sudah ada dalam opsi saat ini
            if ($('#merk_id').find('option[value="' + merk.id + '"]').length === 0) {
                // Jika tidak, tambahkan elemen <option> baru
                var $newOption = new Option(`${merk.merk} (${merk.keterangan})`, merk.id, true, true);
                $('#merk_id').append($newOption).trigger('change');
            } else {
                // Jika sudah ada, langsung atur nilai dan perbarui tampilan
                $('#merk_id').val(id).trigger('change');
            }
            // var $option = $("<option selected></option>").val("ABC").text("ABC");
            // $('#merk_id').append($option).trigger('change');
        }

        const deleteProduct = (id, name) => {
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
                        url: `{{ url('barang/${id}') }}`,
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
                            getProducts();
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
            if (!printPriceProduct.includes(decodedText) && lastResult !== decodedText) {
                lastResult = decodedText;
                ++countResults;
                // Handle on success condition with the decoded message.
                alert(decodedText)
                $("#filterProduct").val(decodedText);
                getProducts();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal',
                    text: 'Produk sudah ditambahkan',
                    showConfirmButton: false,
                    timer: 1500
                })
            }
        }

        var html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", {
                fps: 10,
                qrbox: {
                    width: 200,
                    height: 150
                },
                rememberLastUsedCamera: false,
            });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
@endpush
