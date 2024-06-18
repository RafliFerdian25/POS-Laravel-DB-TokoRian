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
                    <div>Pelanggan
                        <div class="page-title-subheading">
                            Daftar Pelanggan
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
                            <div class="widget-heading col-10 widget__title">Jumlah Pelanggan</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="countCustomer">-</span></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Add Customer --}}
            <div class="col-sm-12 col-md-8 col-xl-9 p-3">
                <div class="main-card mb-3 card">
                    <form id="formAddCustomer">
                        <div class="card-body">
                            <h5 class="card-title text-center">Tambah Pelanggan</h5>
                            @csrf
                            <div class="form-group form-show-validation row">
                                <label for="name" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-left">
                                    Nama
                                </label>
                                <div class="col-lg-9 col-md-9 col-sm-8">
                                    <input type="text" name="name" id="name" class="form-control rounded__10"
                                        required>
                                </div>
                            </div>
                            <div class="form-group form-show-validation row">
                                <label for="nik" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-left">
                                    NIK
                                </label>
                                <div class="col-lg-9 col-md-9 col-sm-8">
                                    <input type="number" class="form-control rounded__10" id="nik" name="nik"
                                        max="9999999999999999" required>
                                </div>
                            </div>
                            <div class="form-group form-show-validation row">
                                <label for="address" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-left">
                                    Alamat
                                </label>
                                <div class="col-lg-9 col-md-9 col-sm-8">
                                    <textarea class="form-control rounded__10" id="address" name="address"></textarea>
                                </div>
                            </div>
                            <div class="form-group form-show-validation row">
                                <label for="phone" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-left">
                                    Telepon
                                </label>
                                <div class="col-lg-9 col-md-9 col-sm-8">
                                    <input type="number" class="form-control rounded__10" id="phone" name="phone"
                                        min="9999999999" max="99999999999999">
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
            {{-- end Form Add Customer --}}
        </div>
        <!-- END CARD DASHBOARD -->

        <!-- Daftar Customer -->
        <div class="ListCustomerSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Daftar Pelanggan</h5>
                    <table class="display nowrap" style="width:100%" id="tableCustomer">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Alamat</th>
                                <th>Telepon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableCustomerBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end Daftar Customer -->
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
            var config = {
                "columnDefs": [{
                    "targets": [0, 1, 3, 5],
                    "className": "text-center"
                }],
            }
            initializeDataTable('tableCustomer', config)

            getCustomer();
        });

        const getCustomer = (typeReport) => {
            $('#tableCustomer').DataTable().clear().draw();
            $('#tableCustomerBody').html(tableLoader(8, `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                type: "GET",
                url: `{{ route('customer.data') }}`,
                data: $('#formFilterCustomer').serialize(),
                dataType: "json",
                success: function(response) {
                    $('#countCustomer').html(response.data.customers.length);
                    if (response.data.customers.length > 0) {
                        $.each(response.data.customers, function(index, customer) {
                            var rowData = [
                                index + 1,
                                customer.nama,
                                customer.nik,
                                customer.alamat,
                                customer.telpon,
                                `<button class="btn btn-sm btn-primary" onclick='showEditCustomer(${JSON.stringify(customer)})'><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCustomer('${customer.id}')"><i class="bi bi-trash"></i></button>`
                            ];
                            var rowNode = $('#tableCustomer').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();

                            // $(rowNode).find('td').eq(0).addClass('text-center');
                            // $(rowNode).find('td').eq(4).addClass('text-center text-nowrap');
                        });
                    } else {
                        $('#tableCustomerBody').html(tableEmpty(8,
                            'barang'));
                    }
                }
            });

        }

        $('#formAddCustomer').validate({
            rules: {
                name: {
                    required: true,
                },
                nik: {
                    required: true,
                    number: true,
                    min: 999999999999999,
                    max: 9999999999999999,
                },
                address: {
                    required: true,
                },
                phone: {
                    required: true,
                    number: true,
                    min: 9999999999,
                    max: 99999999999999,
                },
            },
            messages: {
                name: {
                    required: "Nama tidak boleh kosong",
                },
                nik: {
                    required: "NIK tidak boleh kosong",
                    number: "NIK harus berupa angka",
                    min: "NIK minimal 16 karakter",
                    max: "NIK maksimal 16 karakter",
                },
                address: {
                    required: "Alamat tidak boleh kosong",
                },
                phone: {
                    required: "Telepon tidak boleh kosong",
                    number: "Telepon harus berupa angka",
                    min: "Telepon minimal 10 karakter",
                    max: "Telepon maksimal 14 karakter",
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
                $('#submitButton').html(
                    '<svg class="spinners-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M12 22c5.421 0 10-4.579 10-10h-2c0 4.337-3.663 8-8 8s-8-3.663-8-8c0-4.336 3.663-8 8-8V2C6.579 2 2 6.58 2 12c0 5.421 4.579 10 10 10z"></path></svg>'
                );
                $('#submitButton').prop('disabled', true);
                $.ajax({
                    url: `{{ route('customer.store') }}`,
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: $('#name').val(),
                        nik: $('#nik').val(),
                        address: $('#address').val(),
                        phone: $('#phone').val(),
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
                                getCustomer();
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

        const showEditCustomer = (customer) => {
            Swal.fire({
                title: "Ubah Data Pelanggan",
                html: `
                <form id="formEditCustomer">
                    @csrf
                    <div class="">
                        <label for="nameUpdate" class="col-sm-4 col-form-label">Nama</label>
                        <div class="">
                            <input type="text" name="nameUpdate" id="nameUpdate" class="form-control rounded__10" value="${customer.nama}" required>
                        </div>
                    </div>
                    <div class="">
                        <label for="nikUpdate" class="col-sm-4 col-form-label">NIK</label>
                        <div class="">
                            <input type="number" class="form-control rounded__10" id="nikUpdate" name="nikUpdate" max="9999999999999999" value="${customer.nik}" required>
                        </div>
                    </div>
                    <div class="">
                        <label for="addressUpdate" class="col-sm-4 col-form-label">Alamat</label>
                        <div class="">                                    
                            <textarea class="form-control rounded__10" id="addressUpdate" name="addressUpdate">${customer.alamat}</textarea>
                        </div>
                    </div>
                    <div class="">
                        <label for="phoneUpdate" class="col-sm-4 col-form-label">Telepon</label>
                        <div class="">
                            <input type="number" class="form-control rounded__10" id="phoneUpdate" name="phoneUpdate" min="9999999999" max="99999999999999" value="${customer.telpon}">
                        </div>
                    </div>
                </form>
                `,
                showCancelButton: true,
                confirmButtonText: "Ubah",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-danger"
                },
                customId: {
                    confirmButton: "updateButton"
                },
                didOpen: () => {
                    // Add event listener for enter key
                    const form = document.getElementById('formEditCustomer');
                    form.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault(); // Prevent form submission
                            Swal.getConfirmButton().click(); // Trigger the confirm button
                        }
                    });
                },
                preConfirm: () => {
                    const nameUpdate = Swal.getPopup().querySelector('#nameUpdate').value;
                    const nikUpdate = Swal.getPopup().querySelector('#nikUpdate').value;
                    const addressUpdate = Swal.getPopup().querySelector('#addressUpdate').value;
                    const phoneUpdate = Swal.getPopup().querySelector('#phoneUpdate').value;

                    if (!nameUpdate) {
                        Swal.showValidationMessage(`Jumlah Barang tidak boleh kosong`);
                    }
                    if (!nikUpdate) {
                        Swal.showValidationMessage(`Tanggal tidak boleh kosong`);
                    }
                    if (!addressUpdate) {
                        Swal.showValidationMessage(`Harga tidak boleh kosong`);
                    }
                    if (!phoneUpdate) {
                        Swal.showValidationMessage(`Harga tidak boleh kosong`);
                    }

                    return {
                        nameUpdate: nameUpdate,
                        nikUpdate: nikUpdate,
                        addressUpdate: addressUpdate,
                        phoneUpdate: phoneUpdate,
                    }
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Uabh Data Pelanggan',
                        text: 'Sedang mengubah data pelanggan...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading();
                        },
                    });
                    $.ajax({
                        type: "POST",
                        url: `{{ url('pelanggan/${customer.id}') }}`,
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "PUT",
                            name: result.value.nameUpdate,
                            nik: result.value.nikUpdate,
                            address: result.value.addressUpdate,
                            phone: result.value.phoneUpdate,
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
                                    getCustomer()
                                });
                        },
                        error: function(xhr, status, error) {
                            if (xhr.responseJSON) {
                                errorAlert("Gagal!",
                                    `Ubah data customer Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`
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
                        text: "Ubah data customer dibatalkan",
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

        const deleteCustomer = (idCustomer) => {
            Swal.fire({
                title: 'Hapus data pelanggan',
                text: `Apakah Anda yakin ingin menghapus data pelanggan?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Hapus Data Pelanggan',
                        text: 'Sedang menghapus data pelanggan...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    $.ajax({
                        type: "DELETE",
                        url: `{{ url('pelanggan/${idCustomer}') }}`,
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
                            getCustomer();
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
