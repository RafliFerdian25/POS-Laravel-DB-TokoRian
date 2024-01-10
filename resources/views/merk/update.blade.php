@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- ubah section -->
        <div class="ubah__section">
            <div class="ubah__body">
                <div class="ubah__content card">
                    <div class="title__card text-center">
                        Ubah Merk
                    </div>
                    <form action="{{ route('merk.update', $merk->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="row mb-3">
                            <label for="name" class="col-sm-2 col-form-label">Nama Merk</label>
                            <div class="col-sm-10">
                                <input required value="{{ $merk->name }}" type="text"
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
