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
                    <div>Laporan Barang Kadaluarsa
                        <div class="page-title-subheading">
                            Barang Kadaluarsa
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
                            <div class="widget-heading col-10 widget__title">Total Barang Kadaluarsa</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span>{{ $countProduct }}</span></div>
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
        <!-- Barang Terjual -->
        <div class="barang__terjual__section">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Barang Kadaluarsa</h5>
                    <table class="mb-0 table" id="barang_terjual">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th>Stok</th>
                                <th>Tanggal Kadaluarsa</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td scope="row">{{ $product->IdBarang }}</td>
                                    <td>{{ $product->nmBarang }}</td>
                                    <td>{{ $product->stok }}</td>
                                    <td>{{ $product->expDate }}</td>
                                    <td>
                                        {{-- <a href="{{ route('barang.edit',[$product->IdBarang, "expired"]) }}"
                                            class="btn btn-primary">Ubah</a> --}}
                                        <button onclick="showEdit('{{ $product->IdBarang }}', 'expired')"
                                            class="btn btn-primary">Ubah</button>
                                    </td>
                                </tr>
                            @endforeach
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
            $("#laporan_bulanan").DataTable({
                pageLength: 3,
                paging: false,
                info: false,
            });
        });

        function laporanBulanan(input) {
            let formBulan = $("#formBulan");
            formBulan.submit();
            // Upload log ke server
        }

        function showEdit(idBarang, status) {
            console.log(idBarang);
            // Mengisi konten modal dengan data yang sesuai
            let modalContent = $('#modalMain .modal-content');

            // mengirim request ajax
            $.ajax({
                type: "GET",
                url: `{{ url('/barang/${idBarang}/edit/expired') }}`,
                success: function(response) {
                    modalContent.html(`
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ url('/barang/${response.product.IdBarang}') }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <input hidden type="text" name="type" value="expired">
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
                                        id="nmBarang" name="nmBarang">
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
                                <label for="expDate" class="col-sm-2 col-form-label">Expired Date</label>
                                <div class="col-sm-10">
                                    <input required value="${response.product.expDate}" type="date"
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
                                            return `<option value="${category.jenis}" ${category.jenis == response.product.jenis ? "selected" : ""}>${category.jenis}</option>`
                                        })}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                        </form>
                    `);
                }
            });



            // Menampilkan modal
            $('#modalMain').modal('show');
        }
    </script>
@endpush
