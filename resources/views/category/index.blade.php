@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- TITLE KATEGORI -->
        <div class="app-page-title row justify-content-lg-between">
            <div class="page-title-wrapper col-3">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-folder icon-gradient bg-plum-plate">
                        </i>
                    </div>
                    <div>Kategori
                        <div class="page-title-subheading">
                            Dashboard
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 text-center align-self-center">
                <a href="{{ url('/kategori/create') }}">
                    <button class="btn btn-primary rounded-pill px-3" id="tambah-kategori">Tambah</button>
                </a>
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
                            <div class="widget-heading col-10 widget__title">Total Kategori</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="countCategory">-</span>
                            </div>
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

        <!-- Kategori Section -->
        <div class="kategori__section">
            <!-- Barang -->
            <div class="kategori__container">
                <div class="kategori__content">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title text-center font-size-xlg">Kategori</h5>
                            <table class="display nowrap" style="width:100%" id="tableCategory">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Keterangan</th>
                                        <th>Jumlah Barang</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableCategoryBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End kategori section -->
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
            initializeDataTable('tableCategory');

            getCategories()
        });

        const getCategories = () => {
            $('#tableCategory').DataTable().clear().draw();
            $('#tableCategoryBody').html(tableLoader(5));

            $.ajax({
                type: "GET",
                url: `{{ route('category.data') }}`,
                success: function(response) {
                    $('#countCategory').html(response.data.categories.length);
                    if (response.data.categories.length > 0) {
                        $.each(response.data.categories, function(index, category) {
                            var rowData = [
                                index + 1,
                                category.jenis,
                                category.keterangan,
                                category.products_count,
                                `<button class="btn btn-link btn-lg float-left px-0" onclick="showEdit('${category.ID}')"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCategory('${category.ID}')"><i class="bi bi-trash"></i></button>`
                            ];
                            var rowNode = $('#tableCategory').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();
                        });
                    } else {
                        $('#tableCategoryBody').html(tableEmpty(11,
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
                url: `{{ url('/kategori/${id}/edit') }}`,
                success: function(response) {
                    let formId = `formEditCategory${id}`;

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
                                    <label for="id" class="col-sm-2 col-form-label">ID Kategori</label>
                                    <div class="col-sm-10">
                                        <input required value="${response.data.category.ID}" type="text"
                                            class="form-control rounded__10" maxlength="3"
                                            id="id" name="id" style="text-transform:uppercase">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="name" class="col-sm-2 col-form-label">Nama Kategori</label>
                                    <div class="col-sm-10">
                                        <input required value="${response.data.category.keterangan}" type="text"
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
                                required: "Kode kategori tidak boleh kosong",
                                maxlength: "Kode kategori maksimal 15 karakter",
                            },
                            name: {
                                required: "Nama kategori tidak boleh kosong",
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
                                url: `{{ url('/kategori/${response.data.category.ID}') }}`,
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
                                            getCategories();
                                            // menyembunyikan modal
                                            $('#modalEdit').modal('hide');
                                        });
                                },
                                error: function(xhr, status, error) {
                                    $('#updateButton').html('Ubah');
                                    $('#updateButton').prop('disabled', false);
                                    if (xhr.responseJSON) {
                                        errorAlert("Gagal!",
                                            `Ubah kategori gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`
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

        const deleteCategory = (id) => {
            Swal.fire({
                title: 'Apakah anda yakin hapus kategori?',
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
                        title: 'Hapus Kategori',
                        text: 'Sedang menghapus kategori...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading();
                        },
                    });
                    $.ajax({
                        type: "DELETE",
                        url: `{{ url('/kategori/${id}') }}`,
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Hapus Kategori Berhasil',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            getCategories();
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            if (xhr.responseJSON) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: `Hapus Kategori Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`,
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
