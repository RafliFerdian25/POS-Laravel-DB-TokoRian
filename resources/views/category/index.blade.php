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
        <!-- Kategori Section -->
        <div class="kategori__section">
            <!-- Barang -->
            <div class="container kategori__container">
                <div class="kategori__content">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title text-center font-size-xlg">Kategori</h5>
                            <table class="mb-0 table table__kategori" id="kategori">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Kategori</th>
                                        <th>Jumlah Barang</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $category->jenis }}</td>
                                            <td>{{ $category->keterangan }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    <a href="{{ route('kategori.edit', $category->ID) }}"
                                                        class="btn btn-link btn-lg float-left px-0"><i
                                                            class="fa fa-edit"></i></a>
                                                    <form action="{{ route('kategori.destroy', $category->ID) }}"
                                                        method="POST">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return confirm('Yakin ingin menghapus kategori')"
                                                            class="btn btn-link btn-lg float-right px-0 color__red1"><i
                                                                class="fa fa-trash"></i></button>
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
        <!-- End kategori section -->
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
        <!-- Merk Section -->
        <div class="merk__section">
            <!-- Barang -->
            <div class="container merk__container">
                <div class="merk__content">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title text-center font-size-xlg">Merk</h5>
                            <table class="mb-0 table table__merk" id="merk">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Merk</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($merks as $merk)
                                        <tr>
                                            <td>{{ $merk->ID }}</td>
                                            <td>{{ $merk->name }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    <a href="" class="btn btn-link btn-lg float-left px-0"><i
                                                            class="fa fa-edit"></i></a>
                                                    <form action="" method="POST">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return confirm('Yakin ingin menghapus merk')"
                                                            class="btn btn-link btn-lg float-right px-0 color__red1"><i
                                                                class="fa fa-trash"></i></button>
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
        <!-- End merk section -->
    </div>
    <!-- END Section layouts   -->
@endsection
