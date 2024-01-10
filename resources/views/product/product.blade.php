@extends('layouts.main')

@section('content')
{{-- @dd($products[0]->id) --}}
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- Barang section -->
        <!-- TITLE -->
        <div class="app-page-title row justify-content-lg-between">
            <div class="page-title-wrapper col-3">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-drawer icon-gradient bg-plum-plate">
                        </i>
                    </div>
                    <div>Produk
                        <div class="page-title-subheading">
                            Dashboard
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 text-center align-self-center">
                <a href="{{ url('/barang/create') }}">
                    <button class="btn btn-primary rounded-pill px-3" id="tambah-produk">Tambah</button>
                </a>
            </div>
        </div>
        <!-- END TITLE -->
        <div class="barang__section">
            <!-- Barang -->
            <div class="container barang__container">
                <div class="barang__content">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title text-center font-size-xlg">Produk</h5>
                            <table class="mb-0 table table__barang" id="table__barang">
                                <thead>
                                    <tr>
                                        <th>Barcode</th>
                                        <th>Nama</th>
                                        <th>Satuan</th>
                                        <th>Harga Pokok</th>
                                        <th>Harga Jual</th>
                                        <th>Harga Grosir</th>
                                        <th>Stok</th>
                                        <th>Kadaluarsa</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                @foreach ($products as $product)
                                    <tr id="index_{{ $product->id }}">
                                        <td>{{ $product->id }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->unit }}</td>
                                        <td>{{ $product->purchase_price }}</td>
                                        <td>{{ $product->selling_price }}</td>
                                        <td>{{ $product->wholesale_price }}</td>
                                        <td>{{ $product->stock }}</td>
                                        <td>{{ $product->expired_date }}</td>
                                        <td>
                                            <a href="{{ route('barang.edit', $product->id) }}"
                                                class="btn btn-link btn-lg float-left px-0" id="{{ $product->id }}"><i
                                                    class="fa fa-edit"></i></a>
                                            <a href="#"
                                                onclick="deleteData('{{ route('barang.destroy', $product->id) }}','{{ $product->id }}')"
                                                class="btn btn-link btn-lg float-right px-0 color__red1"
                                                id="{{ $product->id }}"><i class="fa fa-trash"></i></a>
                                        </td>
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
        <!-- end Barang section -->
    </div>
    <!-- END Section layouts   -->
@endsection
@push('scripts')
    <script>
        let table;

        function deleteData(url, idBarang) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, {
                        '_token': $("meta[name='csrf-token']").attr('content'),
                        '_method': 'delete'
                    })
                    .done((response) => {
                        $(`#index_`+ idBarang).remove();
                        alert('success') ;
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menghapus data');
                        return;
                    });
            }
        }
        $(function() {
            table = $('#table__barang').DataTable();
        })

        // $(function() {
        //     table = $('#table__barang').DataTable({
        //         responsive: true,
        //         select: true,
        //         processing: true,
        //         serverSide: true,
        //         ajax: {
        //             url: '{{ route('barang.data') }}',
        //         },
        //         columns: [{
        //                 data: 'id'
        //             },
        //             {
        //                 data: 'name'
        //             },
        //             {
        //                 data: 'unit'
        //             },
        //             {
        //                 data: 'purchase_price'
        //             },
        //             {
        //                 data: 'selling_price'
        //             },
        //             {
        //                 data: 'wholesale_price'
        //             },
        //             {
        //                 data: 'stock'
        //             },
        //             {
        //                 data: 'expired_date'
        //             },
        //             {
        //                 data: 'action',
        //                 orderable: false,
        //                 searchable: false
        //             }
        //         ],
        //     });
        // });
    </script>
@endpush
