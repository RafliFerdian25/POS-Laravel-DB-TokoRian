@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- TITLE PENGELUARAN -->
        <div class="app-page-title row justify-content-lg-between">
            <div class="page-title-wrapper col-3">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-folder icon-gradient bg-plum-plate">
                        </i>
                    </div>
                    <div>Pengeluaran
                        <div class="page-title-subheading">
                            Dashboard
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 text-center align-self-center">
                <a href="{{ url('/pengeluaran/create') }}">
                    <button class="btn btn-primary rounded-pill px-3" id="tambah-pengeluaran">Tambah</button>
                </a>
            </div>
        </div>
        <!-- END TITLE -->

        <!-- CARD DASHBOARD -->
        <div class="row">
            <!-- total pengeluaran -->
            <div class="col-sm-6 col-md-4 col-xl-3 p-3">
                <div class="card mb-0 widget-content row">
                    <div class="content">
                        <div class="widget-content-left row mb-2">
                            <i class="pe-7s-cash col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Pengeluaran</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="countExpense">-</span>
                            </div>
                            <div class="perubahan row">
                                {{-- <div class="widget-subheading col-10" id="total_pengeluaran">
                                    -2000000
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END CARD DASHBOARD -->

        <!-- Pengeluaran Section -->
        <div class="pengeluaran__section">
            <!-- Barang -->
            <div class="pengeluaran__container">
                <div class="pengeluaran__content">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title text-center font-size-xlg">Pengeluaran</h5>
                            <table class="display nowrap" style="width:100%" id="tableExpense">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Jumlah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableExpenseBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End pengeluaran section -->
    </div>
    <!-- END Section layouts   -->
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
    <script>
        $(document).ready(function() {
            initializeDataTable('tableExpense', {
                "columnDefs": [{
                    "targets": 3,
                    "className": "text-center"
                }],
            });

            getExpenses()
        });

        const getExpenses = () => {
            $('#tableExpense').DataTable().clear().draw();
            $('#tableExpenseBody').html(tableLoader(5));

            $.ajax({
                type: "GET",
                url: `{{ route('expense.data') }}`,
                success: function(response) {
                    $('#countExpense').html(response.data.expenses.length);
                    if (response.data.expenses.length > 0) {
                        $.each(response.data.expenses, function(index, expense) {
                            var rowData = [
                                index + 1,
                                expense.created_at,
                                expense.nama,
                                expense.jumlah,
                                `<button class="btn btn-sm btn-warning" onclick="showEdit('${expense.ID}')">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteExpense('${expense.ID}')"><i class="bi bi-trash"></i></button>`
                            ];
                            var rowNode = $('#tableExpense').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();
                        });
                    } else {
                        $('#tableExpenseBody').html(tableEmpty(11,
                            'barang'));
                    }
                }
            });
        }

        function showEdit(id) {
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
                url: `{{ url('/pengeluaran/${id}/edit') }}`,
                success: function(response) {
                    let formId = `formEditExpense${id}`;

                    modalContent.html(`
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Data Barang</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="${formId}">
                        @method('PUT')
                        @csrf
                            <div class="modal-body">
                                @method('PUT')
                                @csrf
                                <div class="row mb-3">
                                    <label for="id" class="col-sm-2 col-form-label">ID Pengeluaran</label>
                                    <div class="col-sm-10">
                                        <input required value="${response.data.expense.ID}" type="text"
                                            class="form-control rounded__10" maxlength="3"
                                            id="id" name="id" style="text-transform:uppercase">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="name" class="col-sm-2 col-form-label">Nama Pengeluaran</label>
                                    <div class="col-sm-10">
                                        <input required value="${response.data.expense.keterangan}" type="text"
                                            class="form-control rounded__10"
                                            id="name" name="name">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" id="updateButton" class="btn btn-primary">Ubah</button>
                            </div>
                        </form>
                    `);

                    $(`#${formId}`).validate({
                        rules: {
                            id: {
                                required: true,
                                maxlength: 3,
                            },
                            name: {
                                required: true,
                            }
                        },
                        messages: {
                            id: {
                                required: "Kode pengeluaran tidak boleh kosong",
                                maxlength: "Kode pengeluaran maksimal 15 karakter",
                            },
                            name: {
                                required: "Nama pengeluaran tidak boleh kosong",
                            }
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
                                url: `{{ url('/pengeluaran/${response.data.expense.ID}') }}`,
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
                                            getExpenses();
                                            // menyembunyikan modal
                                            $('#modalEdit').modal('hide');
                                        });
                                },
                                error: function(xhr, status, error) {
                                    $('#updateButton').html('Ubah');
                                    $('#updateButton').prop('disabled', false);
                                    if (xhr.responseJSON) {
                                        errorAlert("Gagal!",
                                            `Ubah pengeluaran gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`
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

        const deleteExpense = (id) => {
            Swal.fire({
                title: 'Apakah anda yakin hapus pengeluaran?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Hapus Pengeluaran',
                        text: 'Sedang menghapus pengeluaran...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading();
                        },
                    });
                    $.ajax({
                        type: "DELETE",
                        url: `{{ url('/pengeluaran/${id}') }}`,
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Hapus Pengeluaran Berhasil',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            getExpenses();
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            if (xhr.responseJSON) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: `Hapus Pengeluaran Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`,
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