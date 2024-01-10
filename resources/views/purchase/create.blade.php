@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- tambah section -->
        <div class="tambah__section">
            <div class="tambah__body">
                <div class="tambah__content card">
                    <div class="title__card text-center">
                        Tambah Belanja
                    </div>
                    <form action="/belanja" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label for="supplier" class="col-sm-2 col-form-label">Supplier</label>
                            <div class="col-sm-10">
                                <select required
                                    class="form-select rounded__10 @error('supplier_id')
                                is-invalid
                            @enderror"
                                    name="supplier_id" aria-label="Default select example">
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="submit text-end">
                            <button type="submit" class=" btn btn-primary">Lanjut</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end tambah section -->

    </div>
    <!-- END Section layouts   -->
@endsection
