@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- tambah section -->
        <div class="tambah__section">
            <div class="tambah__body">
                <div class="tambah__content card">
                    <div class="title__card text-center">
                        Tambah Barang
                    </div>
                    <form id="formAddProduct">
                        @csrf
                        <div class="row mb-3">
                            <label for="id" class="col-sm-2 col-form-label">Kode Barang</label>
                            <div class="col-sm-10">
                                <input required type="number" class="form-control rounded__10" id="IdBarang"
                                    name="IdBarang" pattern="[0-9]*" inputmode="numeric">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="name" class="col-sm-2 col-form-label">Nama Barang</label>
                            <div class="col-sm-10">
                                <input required type="text" class="form-control rounded__10 " id="nmBarang"
                                    name="nmBarang" style="text-transform:uppercase">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="satuan" class="col-sm-2 col-form-label">Satuan</label>
                            <div class="col-sm-10">
                                <select required class="form-select rounded__10 " name="satuan" id="satuan"
                                    aria-label="Default select example">
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->satuan }}">{{ $unit->satuan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="isi" class="col-sm-2 col-form-label">Isi</label>
                            <div class="col-sm-10">
                                <input required type="number" class="form-control rounded__10 " min="0"
                                    value="0" id="isi" name="isi">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="hargaPokok" class="col-sm-2 col-form-label">Harga Pokok</label>
                            <div class="col-sm-10">
                                <input required type="number" class="form-control rounded__10 " min="0"
                                    id="hargaPokok" name="hargaPokok">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="hargaJual" class="col-sm-2 col-form-label">Harga Jual</label>
                            <div class="col-sm-10">
                                <input required type="number" class="form-control rounded__10 " min="0"
                                    id="hargaJual" name="hargaJual">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="hargaGrosir" class="col-sm-2 col-form-label">Harga Grosir</label>
                            <div class="col-sm-10">
                                <input required type="number" class="form-control rounded__10 " min="0"
                                    value="0" id="hargaGrosir" name="hargaGrosir">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="stok" class="col-sm-2 col-form-label">Stok</label>
                            <div class="col-sm-10">
                                <input required type="number" class="form-control rounded__10 " min="0"
                                    id="stok" name="stok">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="rak" class="col-sm-2 col-form-label">Rak</label>
                            <div class="col-sm-10">
                                <input required type="number" class="form-control rounded__10 " min="0"
                                    id="rak" name="rak">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="expDate" class="col-sm-2 col-form-label">Tanggal Kadaluarsa</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control rounded__10" id="expDate" name="expDate">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="jenis" class="col-sm-2 col-form-label">Kategori</label>
                            <div class="col-sm-10">
                                <select required class="form-select rounded__10" name="jenis" id="jenis"
                                    aria-label="Default select example">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->ID }}">{{ $category->keterangan }} </option>
                                    @endforeach
                                </select>
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
                        <div class="submit text-end">
                            <button type="submit" id="submitButton" class=" btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end tambah section -->

    </div>
    <!-- END Section layouts   -->
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
            $('#merk_id').select2({
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
        });

        // submit form
        $(`#formAddProduct`).validate({
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
                rak: {
                    required: true,
                    number: true,
                    min: 0
                },
                jenis: {
                    required: true,
                },
                merk_id: {
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
                stok: {
                    required: "Stok tidak boleh kosong",
                    number: "Stok harus berupa angka",
                    min: "Stok minimal 0"
                },
                rak: {
                    required: "Rak tidak boleh kosong",
                    number: "Rak harus berupa angka",
                    min: "Rak minimal 0"
                },
                jenis: {
                    required: "Kategori tidak boleh kosong",
                },
                merk_id: {
                    required: "Merk tidak boleh kosong",
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
                    url: `{{ url('/barang') }}`,
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        IdBarang: $('#IdBarang').val(),
                        nmBarang: $('#nmBarang').val(),
                        satuan: $('#satuan').val(),
                        isi: $('#isi').val(),
                        hargaPokok: $('#hargaPokok').val(),
                        hargaJual: $('#hargaJual').val(),
                        hargaGrosir: $('#hargaGrosir').val(),
                        stok: $('#stok').val(),
                        rak: $('#rak').val(),
                        expDate: $('#expDate').val(),
                        jenis: $('#jenis').val(),
                        merk_id: $('#merk_id').val(),
                    },
                    success: function(response) {
                        $('#submitButton').html('Simpan');
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
                                // menutup modal
                                $('#modalMain').modal('hide');
                                // redirect
                                window.location.href = response.data.redirect;
                            });
                    },
                    error: function(xhr, status, error) {
                        $('#submitButton').html('Simpan');
                        $('#submitButton').prop('disabled', false);
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
    </script>
@endpush
