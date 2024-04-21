@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- tambah section -->
        <div class="tambah__section">
            <div class="tambah__body">
                <div class="tambah__content card">
                    <div class="title__card text-center">
                        Tambah Pengeluaran
                    </div>
                    <form method="POST" id="formAddProductBoxOpen">
                        @csrf
                        <div class="row mb-3">
                            <label for="name" class="col-sm-2 col-form-label">ID Barang Dus</label>
                            <div class="col-sm-10">
                                <input required value="{{ $productBox->IdBarang }}" type="text"
                                    class="form-control rounded__10" id="idProductBox" name="idProductBox" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="name" class="col-sm-2 col-form-label">Nama Barang Dus</label>
                            <div class="col-sm-10">
                                <input required value="{{ $productBox->nmBarang }}" type="text"
                                    class="form-control rounded__10" id="nameProductBox" name="nameProductBox" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="id" class="col-sm-2 col-form-label">Isi Dus</label>
                            <div class="col-sm-10">
                                <input required value="{{ $productBox->isi }}" type="number"
                                    class="form-control rounded__10" id="content" name="content" step="1">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="idProductRetail" class="col-sm-2 col-form-label">Nama
                                / Barcode Barang Retail
                                <span class="required-label">*</span></label>
                            <div class="col-sm-10">
                                <div class="select2-input select2-info" style="width: 100%">
                                    <select id="idProductRetail" name="idProductRetail" class="form-control rounded__10">
                                        <option value="">&nbsp;</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="costOfGoodSoldRetail" class="col-sm-2 col-form-label">Harga
                                Pokok
                                <span class="required-label">*</span></label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control rounded__10" id="costOfGoodSoldRetail"
                                    name="costOfGoodSoldRetail" min="1" disabled>
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
    <script>
        $('#idProductRetail').select2({
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

            // disable false
            $('#costOfGoodSoldRetail').prop('disabled', false);
            $('#costOfGoodSoldRetail').val(data.hargaPokok);
        })

        // submit form
        $(`#formAddProductBoxOpen`).validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 50,
                    minlength: 3,
                },
                amount: {
                    required: true,
                    number: true,
                    min: 0
                },
                place: {
                    required: true,
                }
            },
            messages: {
                name: {
                    required: "Nama barang tidak boleh kosong",
                    maxlength: "Nama barang maksimal 50 karakter",
                    minlength: "Nama barang minimal 3 karakter",
                },
                amount: {
                    required: "Jumlah tidak boleh kosong",
                    number: "Jumlah harus berupa angka",
                    min: "Jumlah minimal 0"
                },
                place: {
                    required: "Letak laci tidak boleh kosong",
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
                event.preventDefault();
                $('#submitButton').html(
                    '<svg class="spinners-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M12 22c5.421 0 10-4.579 10-10h-2c0 4.337-3.663 8-8 8s-8-3.663-8-8c0-4.336 3.663-8 8-8V2C6.579 2 2 6.58 2 12c0 5.421 4.579 10 10 10z"></path></svg>'
                );
                $('#submitButton').prop('disabled', true);
                $.ajax({
                    url: `{{ url('/buka-kardus') }}`,
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        idProductBox: $('#idProductBox').val(),
                        content: $('#content').val(),
                        idProductRetail: $('#idProductRetail').val(),
                        costOfGoodSoldRetail: $('#costOfGoodSoldRetail').val(),
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
                                // redirect
                                window.location.href = response.data.redirect;
                            });
                    },
                    error: function(xhr, status, error) {
                        $('#submitButton').html('Simpan');
                        $('#submitButton').prop('disabled', false);
                        if (xhr.responseJSON) {
                            errorAlert("Gagal!",
                                `Menambah barang gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`
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
