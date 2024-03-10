@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- Laporan section -->
        <!-- TITLE -->
        <div class="app-page-title row justify-content-lg-between">
            <div class="page-title-wrapper col-3">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-note2 icon-gradient bg-plum-plate">
                        </i>
                    </div>
                    <div>Laporan
                        <div class="page-title-subheading">
                            Dashboard
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END TITLE -->
        <div class="laporan__section">
            <!-- Laporan -->
            <div class="container laporan__container">
                <div class="laporan__content">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title text-center font-size-xlg">Laporan</h5>
                            <table class="display nowrap table__laporan" style="width:100%" id="table__laporan">
                                <thead>
                                    <tr>
                                        <th>ID Penjualan</th>
                                        <th>Tanggal</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah</th>
                                        <th>Harga Beli</th>
                                        <th>Harga Jual</th>
                                        <th>Diskon</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                @foreach ($sales as $sale)
                                    <tr id="index_{{ $sale->id }}">
                                        <td>{{ $sale->id }}</td>
                                        <td>{{ $sale->created_at }}</td>
                                        <td>{{ $sale->product_name }}</td>
                                        <td>{{ $sale->qty }}</td>
                                        <td>{{ $sale->purchase_price }}</td>
                                        <td>{{ $sale->selling_price }}</td>
                                        <td>{{ $sale->discount }}</td>
                                        <td>{{ $sale->subtotal }}</td>
                                    </tr>
                                @endforeach
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end Laporan section -->
    </div>
    <!-- END Section layouts   -->
@endsection
@push('scripts')
    <script>
        let table;

        function deleteData(url, idLaporan) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, {
                        '_token': $("meta[name='csrf-token']").attr('content'),
                        '_method': 'delete'
                    })
                    .done((response) => {
                        $(`#index_` + idLaporan).remove();
                        alert('success');
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menghapus data');
                        return;
                    });
            }
        }
        $(function() {
            table = $('#table__laporan').DataTable();
        })
    </script>
@endpush
