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
                    <h5 class="card-title text-center">Barang Habis</h5>
                    <table class="mb-0 table" id="barang_terjual">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th>Stok</th>
                                <th>Tanggal Habis</th>
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
                                        <a href="{{ route('barang.edit', [$product->IdBarang, 'empty']) }}"
                                            class="btn btn-primary">Ubah</a>
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
    </script>
@endpush
