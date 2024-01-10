@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- ubah section -->
        <div class="ubah__section">
            <div class="ubah__body">
                <div class="ubah__content card">
                    <div class="title__card text-center">
                        Ubah Kategori
                    </div>
                    <form action="{{ route('kategori.update', $category->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="row mb-3">
                            <label for="id" class="col-sm-2 col-form-label">ID Kategori</label>
                            <div class="col-sm-10">
                                <input required value="{{ $category->id }}" type="text" maxlength="3" style="text-transform:uppercase"
                                    class="form-control rounded__10 @error('id')
                                is-invalid
                            @enderror"
                                    id="id" name="id">
                                @error('id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="name" class="col-sm-2 col-form-label">Nama Kategori</label>
                            <div class="col-sm-10">
                                <input required value="{{ $category->name }}" type="text"
                                    class="form-control rounded__10 @error('name')
                                is-invalid
                            @enderror"
                                    id="name" name="name">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="submit text-end">
                            <button type="submit" class=" btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end ubah section -->

    </div>
    <!-- END Section layouts   -->
@endsection
