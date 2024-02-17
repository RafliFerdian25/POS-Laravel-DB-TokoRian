@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- tambah section -->
        <div class="tambah__section">
            <div class="tambah__body">
                <div class="tambah__content card">
                    <div class="title__card text-center">
                        Tambah Merk
                    </div>
                    <form method="POST" id="formCreateMerk">
                        @csrf
                        <div class="row mb-3">
                            <label for="name" class="col-sm-2 col-form-label">Nama</label>
                            <div class="col-sm-10">
                                <input required value="{{ old('name') }}" type="text" class="form-control rounded__10"
                                    id="name" name="name">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="description" class="col-sm-2 col-form-label">Keterangan</label>
                            <div class="col-sm-10">
                                <input required value="{{ old('description') }}" type="text"
                                    class="form-control rounded__10" id="description" name="description">
                            </div>
                        </div>
                        <div class="submit text-end">
                            <button type="submit" class="btn btn-primary" id="submitButton">Simpan</button>
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
        $('#formCreateMerk').validate({
            rules: {
                name: {
                    required: true
                },
                description: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Nama Merk harus diisi"
                },
                description: {
                    required: "Deskripsi harus diisi"
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.col-sm-10').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                $('#submitButton').html(
                    '<svg class="spinners-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M12 22c5.421 0 10-4.579 10-10h-2c0 4.337-3.663 8-8 8s-8-3.663-8-8c0-4.336 3.663-8 8-8V2C6.579 2 2 6.58 2 12c0 5.421 4.579 10 10 10z"></path></svg>'
                );
                $('#submitButton').prop('disabled', true);
                $.ajax({
                    url: `{{ route('merk.store') }}`,
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: $('#name').val(),
                        description: $('#description').val()
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
        });
    </script>
@endpush
