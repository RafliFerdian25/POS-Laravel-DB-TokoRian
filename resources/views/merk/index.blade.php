@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- TITLE MERK -->
        <div class="app-page-title row justify-content-lg-between">
            <div class="page-title-wrapper col-3">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-wallet icon-gradient bg-plum-plate">
                        </i>
                    </div>
                    <div>Merk
                        <div class="page-title-subheading">
                            Dashboard
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 text-center align-self-center">
                <a href="{{ url('/merk/create') }}">
                    <button class="btn btn-primary rounded-pill px-3" id="tambah-merk">Tambah</button>
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
                            <div class="widget-heading col-10 widget__title">Total Barang</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="countMerk">-</span></div>
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

        <!-- Merk Section -->
        <div class="merk__section">
            <!-- Barang -->
            <div class="container merk__container">
                <div class="merk__content">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title text-center font-size-xlg">Merk</h5>
                            <table class="mb-0 table table__merk" id="tableMerk">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Keterangan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableMerkBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End merk section -->
    </div>
    <!-- END Section layouts   -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#merk').DataTable();

            getMerks();
        });

        const getMerks = () => {
            $('#tableMerk').DataTable().clear().draw();
            $('#tableMerkBody').html(tableLoader(4));

            $.ajax({
                type: "GET",
                url: `{{ route('merk.data') }}`,
                success: function(response) {
                    $('#countMerk').html(response.data.merks.length);
                    if (response.data.merks.length > 0) {
                        $.each(response.data.merks, function(index, merk) {
                            var rowData = [
                                index + 1,
                                merk.merk,
                                merk.keterangan,
                                `<div class="d-flex justify-content-center">
                                                    <button onclick="showEdit('${merk.id}')" class="btn btn-link btn-lg float-left px-0"><i
                                                            class="fa fa-edit"></i></button>
                                                    <form action="" method="POST">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return confirm('Yakin ingin menghapus merk')"
                                                            class="btn btn-link btn-lg float-right px-0 color__red1"><i
                                                                class="fa fa-trash"></i></button>
                                                    </form>
                                                </div>`
                            ];
                            var rowNode = $('#tableMerk').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();

                            // $(rowNode).find('td').eq(8).addClass('text-center');
                            // $(rowNode).find('td').eq(4).addClass('text-center text-nowrap');
                        });
                    } else {
                        $('#tableMerkBody').html(tableEmpty(4,
                            'barang'));
                    }
                }
            });
        }

        const showEdit = (id) => {
            // Mengisi konten modal dengan data yang sesuai
            let modalContent = $('#modalMain .modal-content');

            modalContent.html(`
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Data Merk</h1>
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
                url: `{{ url('/merk/${id}/edit') }}`,
                success: function(response) {
                    let formId = `formEditMerk${id}`;

                    modalContent.html(`
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Data Barang</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="${formId}">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="name" class="col-sm-2 col-form-label">Nama</label>
                                <div class="col-sm-10">
                                    <input required value="${response.data.merk.merk}" type="text"
                                        class="form-control rounded__10 "
                                        id="name" name="name">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="description" class="col-sm-2 col-form-label">Keterangan</label>
                                <div class="col-sm-10">
                                    <input required value="${response.data.merk.keterangan}" type="text"
                                        class="form-control rounded__10 "
                                        id="description" name="description">
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
                            name: {
                                required: true,
                                maxlength: 50,
                                minlength: 3,
                            },
                            description: {
                                required: true,
                                maxlength: 50,
                                minlength: 3,
                            },
                        },
                        messages: {
                            name: {
                                required: "Nama merk tidak boleh kosong",
                                maxlength: "Nama merk maksimal 50 karakter",
                                minlength: "Nama merk minimal 3 karakter",
                            },
                            description: {
                                required: "Keterangan tidak boleh kosong",
                                maxlength: "Keterangan maksimal 50 karakter",
                                minlength: "Keterangan minimal 3 karakter",
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
                            var formData = new FormData(form);
                            $('#updateButton').html(
                                '<svg class="spinners-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M12 22c5.421 0 10-4.579 10-10h-2c0 4.337-3.663 8-8 8s-8-3.663-8-8c0-4.336 3.663-8 8-8V2C6.579 2 2 6.58 2 12c0 5.421 4.579 10 10 10z"></path></svg>'
                            );
                            $('#updateButton').prop('disabled', true);
                            $.ajax({
                                url: `{{ url('/merk/${response.data.merk.id}') }}`,
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
                                            getMerks();
                                            // menyembunyikan modal
                                            $('#modalMain').modal('hide');
                                        });
                                },
                                error: function(xhr, status, error) {
                                    $('#updateButton').html('Ubah');
                                    $('#updateButton').prop('disabled', false);
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

                }
            });


            // Menampilkan modal
            $('#modalMain').modal('show');
        }
    </script>
@endpush
