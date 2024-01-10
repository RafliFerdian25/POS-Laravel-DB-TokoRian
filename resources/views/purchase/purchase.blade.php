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
                    <div>Belanja
                        <div class="page-title-subheading">
                            Dashboard
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 text-center align-self-center">
                <a href="{{ url('/belanja/create') }}">
                    <button class="btn btn-primary rounded-pill px-3" id="tambah-produk">Tambah</button>
                </a>
            </div>
        </div>
        <!-- END TITLE -->
        <div class="belanja__section">
            <!-- Barang -->
            <div class="container belanja__container">
                <div class="belanja__content">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title text-center font-size-xlg">Belanja</h5>
                            <table class="mb-0 table table__belanja" id="table__belanja">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Kasir</th>
                                        <th>Nama Supplier</th>
                                        <th>Total Item</th>
                                        <th>Total Harga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                @foreach ($purchases as $purchase)
                                    <tr id="index_{{ $purchase->id }}">
                                        <td>{{ $purchase->id }}</td>
                                        <td>{{ $purchase->username }}</td>
                                        <td>{{ $purchase->supplier_name }}</td>
                                        <td>{{ $purchase->total_item }}</td>
                                        <td>{{ $purchase->total_price }}</td>
                                        <td>
                                            <a href="{{ route('belanja.edit', $purchase->id) }}"
                                                class="btn btn-link btn-lg float-left px-0" id="{{ $purchase->id }}"><i
                                                    class="fa fa-edit"></i></a>
                                            <a href="#"
                                                onclick="deleteData('{{ route('belanja.destroy', $purchase->id) }}','{{ $purchase->id }}')"
                                                class="btn btn-link btn-lg float-right px-0 color__red1"
                                                id="{{ $purchase->id }}"><i class="fa fa-trash"></i></a>
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
            table = $('#table__belanja').DataTable();
        })

    </script>
@endpush
