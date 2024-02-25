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
                    <div>Laporan Bulanan
                        <div class="page-title-subheading">
                            Laporan
                        </div>
                        <div class="row justify-content-center justify-content-lg-start">
                            <form id="formBulan" class="col-6 col-lg-12">
                                @csrf
                                <div class="row">
                                    <label for="daterange" class="col">Rentang Tanggal :</label>
                                    <input type="text" name="daterange" id="daterange" class="form-control mb-3 col">
                                </div>

                                <div class="row">
                                    <label for="month" class="col">Bulan :</label>
                                    <input type="month" name="month" id="month" class="form-control mb-3 col"
                                        @if ($typeReport == 'Bulanan') value="{{ date('Y-m') }}" @endif
                                        onchange="getProducts('bulanan')">
                                </div>
                            </form>
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
                            <div class="widget-heading col-10 widget__title">Total Jenis Barang Terjual</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2" id="countProduct">-</div>
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

        <!-- FILTER Barang -->
        <div class="FilterExpiredProductSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Filter Barang</h5>
                    <form id="formFilterProduct" method="GET">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="filterBarcode" class="col-sm-2 col-form-label">Nama / Barcode</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control rounded__10 " id="filterProduct"
                                        name="filterBarcode">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="filterCategory" class="col-sm-2 col-form-label">Kategori</label>
                                <div class="col-sm-10">
                                    <select required class="form-select rounded__10" name="filterCategory"
                                        id="filterCategory" aria-label="Default select example">
                                        <option value="">&nbsp;</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->ID }}">{{ $category->keterangan }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="filterMerk" class="col-sm-2 col-form-label">Merk</label>
                                <div class="col-sm-10">
                                    <div class="select2-input select2-info" style="width: 100%">
                                        <select id="filterMerk" name="filterMerk" class="form-control rounded__10">
                                            <option value="">&nbsp;</option>
                                        </select>
                                    </div>
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

        <!-- Barang -->
        <div class="productSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Barang <span id="filterProductDate"></span></h5>
                    <table class="mb-0 table" id="tableProduct">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th>Stok</th>
                                <th>Tanggal Kadaluarsa</th>
                                <th>Terjual</th>
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
            $("#tableProduct").DataTable({
                pageLength: 10,
                info: false,
            });

            getProducts();
            initSelect2Merk();
        });


        $(function() {
            $('#daterange').daterangepicker({
                opens: 'right',
                maxDate: moment(),
                ranges: {
                    'Hari Ini': [moment(), moment()],
                    'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                    '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                    'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                    'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'Tahun Ini': [moment().startOf('year'), moment().endOf('year')],
                    'Tahun Kemarin': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1,
                        'year').endOf('year')],
                },
                locale: {
                    format: 'DD/MM/YYYY',
                    separator: ' - ',
                    applyLabel: 'Pilih',
                    cancelLabel: 'Batal',
                    fromLabel: 'Dari',
                    toLabel: 'Ke',
                    customRangeLabel: 'Custom',
                    weekLabel: 'W',
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                        'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ],
                    firstDay: 1
                }
            });

            @if ($typeReport == 'Harian')
                $('#daterange').data('daterangepicker').setStartDate(moment('{{ $date[0] }}', 'YYYY-MM-DD')
                    .format('DD/MM/YYYY'));
                $('#daterange').data('daterangepicker').setEndDate(moment('{{ $date[1] }}', 'YYYY-MM-DD')
                    .format('DD/MM/YYYY'));
            @else
                $('#daterange').val(null);
            @endif

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
                $("#month").val(null);
                getProducts('harian');
            });
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val(null);
            });
        });

        const getProducts = (typeReport) => {
            $('#countProduct').html(
                '<svg class="loader" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="25" height="25"><circle cx="50" cy="50" r="45" fill="none" stroke="#3498db" stroke-width="5" stroke-dasharray="89 89" stroke-linecap="round"><animateTransform attributeName="transform" dur="1s" type="rotate" from="0 50 50" to="360 50 50" repeatCount="indefinite" /></circle></svg>'
            );
            $('#tableProduct').DataTable().clear().draw();
            $('#tableProductBody').html(tableLoader(5, `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            if (typeReport == 'harian') {
                $('#month').val(null);
            } else if (typeReport == 'bulanan') {
                $('#daterange').val(null);
            }

            $('#filterProductDate').html("");

            $.ajax({
                type: "GET",
                url: `{{ route('monthly.product.report.data') }}`,
                data: {
                    token: '{{ csrf_token() }}',
                    filterProduct: $('#filterProduct').val(),
                    filterCategory: $('#filterCategory').val(),
                    filterMerk: $('#filterMerk').val(),
                    daterange: $('#daterange').val(),
                    month: $('#month').val(),
                },
                dataType: "json",
                success: function(response) {
                    $('#countProduct').html(response.data.countProduct);
                    $('#filterProductDate').html(response.data.dateString);
                    if (response.data.products.length > 0) {
                        $.each(response.data.products, function(index, product) {
                            var rowData = [
                                index + 1,
                                product.IdBarang,
                                product.nmBarang,
                                product.stok,
                                product.expDate,
                                product.jumlah,
                                `<button class="btn btn-sm btn-warning" onclick="showEdit('${product.IdBarang}', 'expired')">Edit</button>
                                <a href="{{ url('/laporan/barang/${product.IdBarang}') }}" class="btn btn-sm btn-primary">Detail</a>`
                            ];
                            var rowNode = $('#tableProduct').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();

                            // $(rowNode).find('td').eq(0).addClass('text-center');
                            // $(rowNode).find('td').eq(4).addClass('text-center text-nowrap');
                        });
                    } else {
                        $('#tableProductBody').html(tableEmpty(5,
                            'barang'));
                    }
                }
            });
        }

        function handleInput(inputId, otherInputIds) {
            return debounce(function() {
                $('#' + otherInputIds.join(', #')).val(null).trigger('change');
                getProducts();
            }, 750);
        }

        $('#filterProduct').on('input', handleInput('filterProduct', ['filterCategory', 'filterMerk']));
        $('#filterCategory').on('input', handleInput('filterCategory', ['filterProduct', 'filterMerk']));
        $('#filterMerk').on('input', handleInput('filterMerk', ['filterProduct', 'filterCategory']));


        function showEdit(idBarang, status) {
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
                url: `{{ url('/barang/${idBarang}/edit/expired') }}`,
                success: function(response) {
                    modalContent.html(`
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ url('/barang/${response.product.IdBarang}') }}" method="POST" id="formEditProduct">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <input hidden type="text" name="type" value="expired">
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
                                            return `<option value="${category.jenis}" ${category.jenis == response.product.jenis ? "selected" : ""}>${category.jenis}</option>`
                                        })}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                        </form>
                    `);
                }
            });

            // Menampilkan modal
            $('#modalMain').modal('show');
        }

        $("formEditProduct").validate({
            rules: {
                IdBarang: {
                    required: true,
                    maxlength: 15,
                    minlength: 15,
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
                isi: {
                    required: true,
                    number: true,
                    min: 0
                },
                hargaPokok: {
                    required: true,
                    number: true,
                    min: 1000000
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
                expDate: {
                    required: true,
                },
                jenis: {
                    required: true,
                },
            },
            messages: {
                IdBarang: {
                    required: "Kode barang tidak boleh kosong",
                    maxlength: "Kode barang maksimal 15 karakter",
                    minlength: "Kode barang minimal 15 karakter",
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
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
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
                            .then((value) => {
                                if (value === "confirm") {
                                    // window.location.href = response.data.redirect
                                    getProducts();
                                }
                            });

                        setTimeout(function() {
                            // window.location.href = response.data.redirect
                            getProducts();
                        }, 4000);
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
                                `Terjadi kesalahan pada server. Error: ${xhr.responseText}`);
                        }
                        return false;
                    }
                });
            }
        })


        const initSelect2Merk = () => {
            $('#filterMerk').select2({
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
        }
    </script>
@endpush
