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
                    <div>Gas
                        <div class="page-title-subheading">
                            Daftar Gas
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
                            <div class="widget-heading col-10 widget__title">Jumlah Gas Tersisa</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="countGas">{{ $remainingGas }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-8 col-xl-9 p-3">
                <div class="main-card mb-3 card">
                    <form id="formAddGas">
                        <div class="card-body">
                            <h5 class="card-title text-center">Tambah Gas</h5>
                            @csrf
                            <div class="form-group form-show-validation row select2-form-input">
                                <label for="date" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-left">Tanggal
                                </label>
                                <div class="col-lg-9 col-md-9 col-sm-8">
                                    <div class="select2-input select2-info" style="width: 100%">
                                        <input type="date" name="date" id="date" class="form-control rounded__10"
                                            value="{{ date('Y-m') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-show-validation row select2-form-input">
                                <label for="stock" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-left">Jumlah Gas
                                </label>
                                <div class="col-lg-9 col-md-9 col-sm-8">
                                    <input type="number" class="form-control rounded__10" id="stock" name="stock"
                                        value="50">
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
        </div>
        <!-- END CARD DASHBOARD -->

        <!-- FILTER Gas -->
        <div class="FilterGasSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Filter Gas</h5>
                    <form id="formFilterGas" method="GET" onsubmit="event.preventDefault(); getGas();">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="daterange" class="col-sm-2 col-form-label">Rentang Tanggal</label>
                                <div class="col-sm-10">
                                    <input type="text" name="daterange" id="daterange" class="form-control rounded__10">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="month" class="col-sm-2 col-form-label">Bulan</label>
                                <div class="col-sm-10">
                                    <input type="month" name="month" id="month" class="form-control rounded__10"
                                        @if ($typeReport == 'Bulanan') value="{{ date('Y-m') }}" @endif
                                        onchange="getGas('bulanan')">
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
        <!-- end Filter Gas -->

        <!-- Daftar Gas -->
        <div class="ListGasSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Daftar Gas (<span id="dateString"></span>)</h5>
                    <table class="display nowrap" style="width:100%" id="tableGas">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableGasBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end Daftar Gas -->
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
                    "targets": [0, 1, 2, 3],
                    "className": "text-center"
                }],
            }
            initializeDataTable('tableGas', config)
            initDateRange('{{ $typeReport }}', getGas);

            getGas();
        });

        const getGas = (typeReport) => {
            // mengosongkan inputan tanggal
            if (typeReport == 'harian') {
                $('#month').val(null);
            } else if (typeReport == 'bulanan') {
                $('#daterange').val(null);
            }

            $('#tableGas').DataTable().clear().draw();
            $('#tableGasBody').html(tableLoader(8, `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                type: "GET",
                url: `{{ route('gas.data') }}`,
                data: $('#formFilterGas').serialize(),
                dataType: "json",
                success: function(response) {
                    $('#dateString').html(response.data.dateString);
                    if (response.data.gases.length > 0) {
                        $.each(response.data.gases, function(index, gas) {
                            var rowData = [
                                index + 1,
                                moment(gas.tanggal).format('DD-MM-YYYY'),
                                gas.stok,
                                `<a href="{{ url('gas/${gas.id}') }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                <button class="btn btn-sm btn-primary" onclick='showEditGas(${JSON.stringify(gas)})'><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteGas('${gas.id}')"><i class="bi bi-trash"></i></button>`
                            ];
                            var rowNode = $('#tableGas').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();

                            // $(rowNode).find('td').eq(0).addClass('text-center');
                            // $(rowNode).find('td').eq(4).addClass('text-center text-nowrap');
                        });
                    } else {
                        $('#tableGasBody').html(tableEmpty(8,
                            'barang'));
                    }
                }
            });

        }

        $('#formAddGas').validate({
            rules: {
                date: {
                    required: true,
                },
                stock: {
                    required: true,
                    number: true,
                },
            },
            messages: {
                date: {
                    required: "Tanggal gas datang tidak boleh kosong",
                },
                stock: {
                    required: "Stok gas yang diterima tidak boleh kosong",
                    number: "Stok gas yang diterima harus berupa angka",
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
                    url: `{{ route('gas.store') }}`,
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        date: $('#date').val(),
                        stock: $('#stock').val(),
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
                                getGas();
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

        const showEditGas = (gas) => {
            Swal.fire({
                title: "Ubah Data Gas",
                html: `
                <form id="formEditGas">
                    @csrf
                    <div class="">
                        <label for="dateUpdate" class="col-sm-4 col-form-label">Tanggal</label>
                        <div class="">
                            <input required type="date" class="form-control rounded__10 " id="dateUpdate" name="dateUpdate" min="1" value="${moment(gas.tanggal).format('YYYY-MM-DD')}">
                        </div>
                    </div>
                    <div class="">
                        <label for="stockUpdate" class="col-sm-4 col-form-label">Jumlah Gas</label>
                        <div class="">
                            <input required type="number" class="form-control rounded__10 " id="stockUpdate" name="stockUpdate" min="1" value="${gas.stok}">
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
                    const form = document.getElementById('formEditGas');
                    form.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault(); // Prevent form submission
                            Swal.getConfirmButton().click(); // Trigger the confirm button
                        }
                    });
                },
                preConfirm: () => {
                    const stockUpdate = Swal.getPopup().querySelector('#stockUpdate').value;
                    const dateUpdate = Swal.getPopup().querySelector('#dateUpdate').value;

                    if (!stockUpdate) {
                        Swal.showValidationMessage(`Jumlah Barang tidak boleh kosong`);
                    }

                    if (!dateUpdate) {
                        Swal.showValidationMessage(`Tanggal tidak boleh kosong`);
                    }

                    return {
                        stockUpdate: stockUpdate,
                        dateUpdate: dateUpdate,
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: `{{ url('gas/${gas.id}') }}`,
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "PUT",
                            IdGas: gas.id,
                            stock: result.value.stockUpdate,
                            date: result.value.dateUpdate,
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
                                    getGas()
                                });
                        },
                        error: function(xhr, status, error) {
                            if (xhr.responseJSON) {
                                errorAlert("Gagal!",
                                    `Ubah data gas Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`
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
                        text: "Ubah data gas dibatalkan",
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

        const deleteGas = (idGas) => {
            Swal.fire({
                title: 'Hapus data gas',
                text: `Apakah Anda yakin ingin menghapus data gas?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Hapus Data Gas',
                        text: 'Sedang menghapus data gas...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    $.ajax({
                        type: "DELETE",
                        url: `{{ url('gas/${idGas}') }}`,
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
                            getGas();
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
