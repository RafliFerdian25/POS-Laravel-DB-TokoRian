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
                    <div>Laporan Barang Habis
                        <div class="page-title-subheading">
                            Barang Habis
                        </div>
                    </div>
                </div>
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
                            <div class="widget-heading col-10 widget__title">Total Barang Habis</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="countProduct">-</span></div>
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
        {{--  --}}
        <!-- FILTER Barang Habis -->
        <div class="FilterEmptyProductSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Filter Barang Habis</h5>
                    <form id="formFilterProduct" method="GET" onsubmit="event.preventDefault(); getEmptyProduct();">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="filterName" class="col-sm-2 col-form-label">Nama Barang</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control rounded__10 " id="filterName"
                                        name="filterName">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="filterCategory" class="col-sm-2 col-form-label">Kategori</label>
                                <div class="col-sm-10">
                                    <select class="form-select rounded__10 " name="filterCategory"
                                        aria-label="Default select example">
                                        <option value="" selected>Pilih Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->ID }}">{{ $category->keterangan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="filterStock" class="col-sm-2 col-form-label">Stok (Kurang Dari)</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control rounded__10 " id="filterStock"
                                        name="filterStock">
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
        <!-- end barang terjual -->

        <!-- Barang Habis -->
        <div class="productSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Barang Habis</h5>
                    <table class="mb-0 table" id="tableEmptyProduct">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th>Stok</th>
                                <th>Tanggal Habis</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableEmptyProductBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end barang terjual -->
    </div>
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
            $("#tableEmptyProduct").DataTable({
                pageLength: 10,
                info: false,
            });

            getEmptyProduct();
        });

        const getEmptyProduct = () => {
            $('#tableEmptyProduct').DataTable().clear().draw();
            $('#tableEmptyProductBody').html(tableLoader(5, `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                type: "GET",
                url: `{{ route('barang.habis.data') }}`,
                data: $('#formFilterProduct').serialize(),
                dataType: "json",
                success: function(response) {
                    $('#countProduct').html(response.data.countProduct);
                    if (response.data.products.length > 0) {
                        $.each(response.data.products, function(index, product) {
                            var rowData = [
                                product.IdBarang,
                                product.nmBarang,
                                product.stok,
                                product.expDate,
                                `<button class="btn btn-sm btn-warning" onclick="showEdit('${product.IdBarang}')">Edit</button>`
                            ];
                            var rowNode = $('#tableEmptyProduct').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();

                            // $(rowNode).find('td').eq(0).addClass('text-center');
                            // $(rowNode).find('td').eq(4).addClass('text-center text-nowrap');
                        });
                    } else {
                        $('#tableEmptyProductBody').html(tableEmpty(5,
                            'barang habis'));
                    }
                }
            });
        }

        function showEdit(idBarang, status) {
            // Mengisi konten modal dengan data yang sesuai
            let modalContent = $('#modalMain .modal-content');

            // mengirim request ajax
            $.ajax({
                type: "GET",
                url: `{{ url('/barang/${idBarang}/edit') }}`,
                success: function(response) {
                    let formId = `formEditProduct${idBarang}`;

                    modalContent.html(`
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="${formId}">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="IdBarang" class="col-sm-2 col-form-label">Kode Barang</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.IdBarang}" type="text"
                                        class="form-control rounded__10 "
                                        id="IdBarang" name="IdBarang" max="999999999999999" pattern="[0-9]*" inputmode="numeric">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="nmBarang" class="col-sm-2 col-form-label">Nama Barang</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.nmBarang}" type="text"
                                        class="form-control rounded__10 "
                                        id="nmBarang" name="nmBarang" style="text-transform:uppercase">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="satuan" class="col-sm-2 col-form-label">Satuan</label>
                                <div class="col-sm-10">
                                    <select required
                                        class="form-select rounded__10"
                                        name="satuan" aria-label="Default select example">
                                        ${response.units.map((unit) => {
                                            return `<option value="${unit.satuan}" ${unit.satuan == response.product.satuan ? "selected" : ""}>${unit.satuan}</option>`
                                        })}
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="isi" class="col-sm-2 col-form-label">Isi</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.isi}" type="number"
                                        class="form-control rounded__10 "
                                        min="0" id="isi" name="isi">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="hargaPokok" class="col-sm-2 col-form-label">Harga Pokok</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.hargaPokok}" type="number"
                                        class="form-control rounded__10 "
                                        min="0" id="hargaPokok" name="hargaPokok">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="hargaJual" class="col-sm-2 col-form-label">Harga Jual</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.hargaJual}" type="number"
                                        class="form-control rounded__10 "
                                        min="0" id="hargaJual" name="hargaJual">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="hargaGrosir" class="col-sm-2 col-form-label">Harga Grosir</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.hargaGrosir}" type="number"
                                        class="form-control rounded__10 "
                                        min="0" id="hargaGrosir" name="hargaGrosir">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="stok" class="col-sm-2 col-form-label">Stok</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.stok}" type="number"
                                        class="form-control rounded__10 "
                                        min="0" id="stok" name="stok">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="expDate" class="col-sm-2 col-form-label">Tanggal Kadaluarsa</label>
                                <div class="col-sm-10">
                                    <input value="${response.product.expDate}" type="date"
                                        class="form-control rounded__10 "
                                        id="expDate" name="expDate">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="jenis" class="col-sm-2 col-form-label">Kategori</label>
                                <div class="col-sm-10">
                                    <select required
                                        class="form-select rounded__10 "
                                        name="jenis" aria-label="Default select example">
                                        ${response.categories.map((category) => {
                                            return `<option value="${category.ID}" ${category.ID == response.product.jenis ? "selected" : ""}>${category.keterangan}</option>`
                                        })}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" id="updateButton" class="btn btn-primary">Simpan</button>
                        </div>
                        </form>
                    `);

                    $(`#${formId}`).validate({
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
                            jenis: {
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
                            var formData = new FormData(form);
                            $('#updateButton').html(
                                '<svg class="spinners-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M12 22c5.421 0 10-4.579 10-10h-2c0 4.337-3.663 8-8 8s-8-3.663-8-8c0-4.336 3.663-8 8-8V2C6.579 2 2 6.58 2 12c0 5.421 4.579 10 10 10z"></path></svg>'
                            );
                            $('#updateButton').prop('disabled', true);
                            $.ajax({
                                url: `{{ url('/barang/${response.product.IdBarang}') }}`,
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    $('#updateButton').html('Update');
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
                                            getEmptyProduct();
                                            // menyembunyikan modal
                                            $('#modalMain').modal('hide');
                                        });
                                },
                                error: function(xhr, status, error) {
                                    $('#updateButton').html('Update');
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