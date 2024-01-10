@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- TITLE SUPPLIER -->
        <div class="app-page-title row justify-content-lg-between">
            <div class="page-title-wrapper col-3">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-car icon-gradient bg-plum-plate">
                        </i>
                    </div>
                    <div>Supplier
                        <div class="page-title-subheading">
                            Dashboard
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 text-center align-self-center">
                <a href="{{ url('/supplier/create') }}">
                    <button class="btn btn-primary rounded-pill px-3" id="tambah-supplier">Tambah</button>
                </a>
            </div>
        </div>
        <!-- END TITLE -->
        <!-- Supplier Section -->
        <div class="supplier__section">
            <!-- Barang -->
            <div class="container supplier__container">
                <div class="supplier__content">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title text-center font-size-xlg">Supplier</h5>
                            <table class="mb-0 table table__supplier" id="supplier">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Supplier</th>
                                        <th>Alamat</th>
                                        <th>Telepon</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($suppliers as $supplier)
                                        <tr>
                                            <td>{{ $supplier->id }}</td>
                                            <td>{{ $supplier->name }}</td>
                                            <td>{{ $supplier->address }}</td>
                                            <td>{{ $supplier->phone }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-link btn-lg float-left px-0"><i class="fa fa-edit"></i></a>
                                                    <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit" onclick="return confirm('Yakin ingin menghapus supplier')" class="btn btn-link btn-lg float-right px-0 color__red1"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End supplier section -->
    </div>
    <!-- END Section layouts   -->
@endsection
