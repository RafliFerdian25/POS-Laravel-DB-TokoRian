@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- tambah section -->
        <div class="tambah__section">
            <div class="tambah__body">
                <div class="tambah__content card">
                    <div class="title__card text-center">
                        Tambah Supplier
                    </div>
                    <form id="formAddSupplier">
                        @csrf
                        <div class="row mb-3">
                            <label for="Nama" class="col-sm-2 col-form-label">Nama <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input required type="text" maxlength="100" class="form-control rounded__10"
                                    id="Nama" name="Nama">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="Produk" class="col-sm-2 col-form-label">Produk <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input required type="text" maxlength="100" class="form-control rounded__10"
                                    id="Produk" name="Produk">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="alamat" class="col-sm-2 col-form-label">Alamat <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea required type="text" class="form-control rounded__10" id="alamat" name="alamat"></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="kota" class="col-sm-2 col-form-label">Kota <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input required type="text" maxlength="25" class="form-control rounded__10"
                                    id="kota" name="kota">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="telp" class="col-sm-2 col-form-label">Telepon</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control rounded__10" id="telp" name="telp">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="enail" class="form-control rounded__10" id="email" name="email">
                            </div>
                        </div>
                        {{-- start Jadwal --}}
                        <div class="row mb-3">
                            <label for="jadwal" class="col-sm-2 col-form-label">Jadwal</label>
                            <div class="col-sm-10">
                                <select class="form-select rounded__10" id="jadwal" name="jadwal">
                                    <option selected value="">Pilih hari...</option>
                                    <option value="1">Senin</option>
                                    <option value="2">Selasa</option>
                                    <option value="3">Rabu</option>
                                    <option value="4">Kamis</option>
                                    <option value="5">Jumat</option>
                                    <option value="6">Sabtu</option>
                                    <option value="7">Minggu</option>
                                </select>
                            </div>
                        </div>
                        {{-- End Jadwal --}}
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
        $('#formAddSupplier').validate({
            rules: {
                Nama: {
                    required: true,
                    maxlength: 100
                },
                Produk: {
                    required: true,
                    maxlength: 100
                },
                alamat: {
                    required: true
                },
                kota: {
                    required: true,
                    maxlength: 25
                },
                telp: {
                    number: true
                },
                email: {
                    email: true
                },
            },
            messages: {
                Nama: {
                    required: "Nama harus diisi",
                    maxlength: "Nama maksimal 100 karakter"
                },
                Produk: {
                    required: "Produk harus diisi",
                    maxlength: "Produk maksimal 100 karakter"
                },
                alamat: {
                    required: "Alamat harus diisi"
                },
                kota: {
                    required: "Kota harus diisi",
                    maxlength: "Kota maksimal 25 karakter"
                },
                telp: {
                    number: "Telepon harus berupa angka"
                },
                email: {
                    email: "Email tidak valid"
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
            submitHandler: function(form) {
                event.preventDefault();
                $('#submitButton').html(
                    '<svg class="spinners-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M12 22c5.421 0 10-4.579 10-10h-2c0 4.337-3.663 8-8 8s-8-3.663-8-8c0-4.336 3.663-8 8-8V2C6.579 2 2 6.58 2 12c0 5.421 4.579 10 10 10z"></path></svg>'
                );
                $('#submitButton').prop('disabled', true);
                $.ajax({
                    url: "{{ route('supplier.store') }}",
                    type: "POST",
                    data: $('#formAddSupplier').serialize(),
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
