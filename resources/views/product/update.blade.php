@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- ubah section -->
        <div class="ubah__section">
            <div class="ubah__body">
                <div class="ubah__content card">
                    <div class="title__card text-center">
                        Ubah Barang
                    </div>
                    <form action="{{ route('barang.update', $product->IdBarang) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <input hidden type="text" name="type" value="{{ $type }}">
                        <div class="row mb-3">
                            <label for="IdBarang" class="col-sm-2 col-form-label">Kode Barang</label>
                            <div class="col-sm-10">
                                <input required value="{{ $product->IdBarang }}" type="number"
                                    class="form-control rounded__10 @error('IdBarang')
                                is-invalid
                            @enderror"
                                    id="IdBarang" name="IdBarang" max="999999999999999">
                                @error('id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="nmBarang" class="col-sm-2 col-form-label">Nama Barang</label>
                            <div class="col-sm-10">
                                <input required value="{{ $product->nmBarang }}" type="text"
                                    class="form-control rounded__10 @error('nmBarang')
                                is-invalid
                            @enderror"
                                    id="nmBarang" name="nmBarang">
                                @error('nmBarang')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="satuan" class="col-sm-2 col-form-label">Satuan</label>
                            <div class="col-sm-10">
                                <select required
                                    class="form-select rounded__10 @error('satuan')
                                is-invalid
                            @enderror"
                                    name="satuan" aria-label="Default select example">
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit['satuan'] }}"
                                            @if ($unit['satuan'] == $product->satuan) selected @endif>{{ $unit['satuan'] }}</option>
                                    @endforeach
                                </select>
                                @error('satuan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="isi" class="col-sm-2 col-form-label">Isi</label>
                            <div class="col-sm-10">
                                <input required value="{{ $product->isi }}" type="number"
                                    class="form-control rounded__10 @error('isi')
                                is-invalid
                            @enderror"
                                    min="0" id="isi" name="isi">
                                @error('isi')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="hargaPokok" class="col-sm-2 col-form-label">Harga Pokok</label>
                            <div class="col-sm-10">
                                <input required value="{{ $product->hargaPokok }}" type="number"
                                    class="form-control rounded__10 @error('hargaPokok')
                                is-invalid
                            @enderror"
                                    min="0" id="hargaPokok" name="hargaPokok">
                                @error('hargaPokok')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="hargaJual" class="col-sm-2 col-form-label">Harga Jual</label>
                            <div class="col-sm-10">
                                <input required value="{{ $product->hargaJual }}" type="number"
                                    class="form-control rounded__10 @error('hargaJual')
                                is-invalid
                            @enderror"
                                    min="0" id="hargaJual" name="hargaJual">
                                @error('hargaJual')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="hargaGrosir" class="col-sm-2 col-form-label">Harga Grosir</label>
                            <div class="col-sm-10">
                                <input required value="{{ $product->hargaGrosir }}" type="number"
                                    class="form-control rounded__10 @error('hargaGrosir')
                                is-invalid
                            @enderror"
                                    min="0" id="hargaGrosir" name="hargaGrosir">
                                @error('hargaGrosir')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                            <label for="discount" class="col-sm-2 col-form-label">Discount</label>
                            <div class="col-sm-10">
                                <input required value="{{ $product->discount }}" type="number"
                                    class="form-control rounded__10 @error('discount')
                                is-invalid
                            @enderror"
                                    min="0" id="discount" name="discount">
                                @error('discount')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="row mb-3">
                            <label for="stok" class="col-sm-2 col-form-label">Stok</label>
                            <div class="col-sm-10">
                                <input required value="{{ $product->stok }}" type="number"
                                    class="form-control rounded__10 @error('stok')
                                is-invalid
                            @enderror"
                                    min="0" id="stok" name="stok">
                                @error('stok')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="expDate" class="col-sm-2 col-form-label">Expired Date</label>
                            <div class="col-sm-10">
                                <input required value="{{ $product->expDate }}" type="date"
                                    class="form-control rounded__10 @error('expDate')
                                is-invalid
                            @enderror"
                                    id="expDate" name="expDate">
                                @error('expDate')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="jenis" class="col-sm-2 col-form-label">Kategori</label>
                            <div class="col-sm-10">
                                <select required
                                    class="form-select rounded__10 @error('jenis')
                                is-invalid
                            @enderror"
                                    name="jenis" aria-label="Default select example">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->jenis }}"
                                            @if ($category->jenis == $product->jenis) selected @endif>{{ $category->jenis }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                            <label for="merk" class="col-sm-2 col-form-label">Merk</label>
                            <div class="col-sm-10">
                                <select required
                                    class="form-select rounded__10 @error('merk_id')
                                is-invalid
                            @enderror"
                                    name="merk_id" aria-label="Default select example">
                                    @foreach ($merks as $merk)
                                        <option value="{{ $merk->id }}" @if ($merk->id == $product->merk_id)
                                            selected
                                            @endif>{{ $merk->name }} </option>
                                    @endforeach
                                </select>
                                @error('merk_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div> --}}
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
