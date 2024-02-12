@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- ubah section -->
        <div class="ubah__section">
            <div class="ubah__body">
                <div class="ubah__content card">
                    <div class="title__card text-center">
                        Ubah Supplier
                    </div>
                    <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="row mb-3">
                            <label for="name" class="col-sm-2 col-form-label">Nama Supplier</label>
                            <div class="col-sm-10">
                                <input required value="{{ $supplier->name }}" type="text"
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
                        <div class="row mb-3">
                            <label for="address" class="col-sm-2 col-form-label">Alamat</label>
                            <div class="col-sm-10">
                                <textarea required type="text"
                                    class="form-control rounded__10 @error('address')
                                is-invalid
                            @enderror"
                                    id="address" name="address">{{ $supplier->address }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="phone" class="col-sm-2 col-form-label">Telepon</label>
                            <div class="col-sm-10">
                                <input required type="number"
                                    class="form-control rounded__10 @error('phone')
                                is-invalid
                            @enderror"
                                    id="phone" name="phone"
                                    value="@if ($errors->has('phone')) {{ old('phone') }}@else{{ $supplier->phone }} @endif">
                                @error('phone')
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
