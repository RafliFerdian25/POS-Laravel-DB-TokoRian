@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- tambah section -->
        <div class="tambah__section">
            <div class="tambah__body">
                <div class="tambah__content card">
                    <div class="card-body">
                        <div class="title__card text-center">
                            Tambah Pembelian Barang
                        </div>
                        <form method="POST" id="formAddPurchase">
                            @csrf
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
                            <div class="submit text-end">
                                <a href="{{ route('purchase.index') }}" class="btn btn-danger">Batal</a>
                                <button type="submit" id="submitButton" class="btn btn-primary">Lanjut</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="footer__card">
                            <a href="{{ route('supplier.create') }}" class="btn btn-primary">
                                Tambah Supplier
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end tambah section -->

    </div>
    <!-- END Section layouts   -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#supplier_id').select2({
                theme: "bootstrap-5",
                placeholder: 'Masukkan Supplier Barang',
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
                        return "Supplier tidak ditemukan";
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
                                id: supplier.IdSupplier, // Use the supplier
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
            }).on('select2:select', function(e) {
                $(this).removeClass('is-invalid');
            });
        });

        $('#formAddPurchase').validate({
            rules: {
                supplier_id: {
                    required: true
                }
            },
            messages: {
                supplier_id: {
                    required: "Supplier harus diisi"
                }
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
                $('#submitButton').html(
                    '<svg class="spinners-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M12 22c5.421 0 10-4.579 10-10h-2c0 4.337-3.663 8-8 8s-8-3.663-8-8c0-4.336 3.663-8 8-8V2C6.579 2 2 6.58 2 12c0 5.421 4.579 10 10 10z"></path></svg>'
                );
                $('#submitButton').prop('disabled', true);
                $.ajax({
                    type: "POST",
                    url: "{{ route('purchase.store') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        supplier_id: $('#supplier_id').val()
                    },
                    success: function(response) {
                        $('#submitButton').html('Lanjut');
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
                                window.location.href = response.data.redirect;
                            });
                    },
                    error: function(xhr, status, error) {
                        $('#submitButton').html('Lanjut');
                        $('#submitButton').prop('disabled', false);
                        if (xhr.responseJSON) {
                            errorAlert("Gagal!",
                                `Tambah Pembelian Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`
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
        });
    </script>
@endpush
