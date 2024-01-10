@extends('layouts.main')

@section('content')
    <!-- Section Layouts  -->
    <div class="app-main__inner">
        <!-- Kasir Section -->
        <div class="kasir__section row">
            <!-- table kasir -->
            <div class="container col-8 kasir__container">
                <div class="kasir__content">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title text-center font-size-xlg">Kasir</h5>
                            <form class="form-produk" id="submit-tambah-produk">
                                @csrf
                                <div class="form-group row">
                                    <label for="id_produk" class="col-lg-3">Kode Produk</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="hidden" name="id_penjualan" id="id_penjualan"
                                                value="{{ $id_penjualan }}">
                                            <input type="text" class="form-control" name="id_produk" id="id_produk">
                                            <span class="input-group-btn">
                                                <button onclick="tampilProduk()"
                                                    class="btn btn-info btn-flat tampilProdukCoba" type="button"><i
                                                        class="fa fa-arrow-right"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <table class="mb-0 table  table-stiped table-bordered  table-penjualan">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Harga</th>
                                        <th width="10%">Jumlah</th>
                                        <th>Diskon</th>
                                        <th>Subtotal</th>
                                        <th width="5%"><i class="fa fa-cog"></i></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end table kasir -->
            <!-- checkout -->
            <div class="container col-4 checkout__container">
                <div class="checkout__content">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title font-size-xlg mb-3">Checkout</h5>
                            <!-- checkout barang -->
                            <form action="{{ route('transaksi.simpan') }}" class="form-penjualan" method="post">
                                @csrf
                                <input type="hidden" name="id_penjualan" value="{{ $id_penjualan }}">
                                <input type="hidden" name="total" id="total">
                                <input type="hidden" name="total_item" id="total_item">
                                <input type="hidden" name="bayar" id="bayar">

                                <div class="checkout">
                                    <!-- Subtotal -->
                                    <div class="checkout__subtotal row">
                                        <h6 class="col-5">SubTotal</h6>
                                        <input type="text" id="subtotalrp" class="form-control col-7 text-end" readonly>
                                    </div>
                                    <!-- diskon -->
                                    <div class="checkout__diskon row">
                                        <h6 class="col-5 color__abu">Diskon</h6>
                                        <input type="text" name="diskon" id="diskon" value="0"
                                            pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency"
                                            class="form-control col-7 color__abu text-end">
                                    </div>
                                </div>
                                <div class="garis__putus my-3"></div>
                                <!-- total -->
                                <div class="checkout row">
                                    <h5 class="col-5">Total</h5>
                                    <input type="text" id="totalbayar" class="form-control col-7 text-end" readonly>
                                </div>
                                <!-- Bayar -->
                                <div class="checkout row">
                                    <label for="bayar" class="col-5">
                                        <h5>Bayar</h5>
                                    </label>
                                    <input type="number" id="dibayar" class="form-control" name="dibayar" value="0">
                                </div>
                                <!-- Kembalian -->
                                <div class="checkout row">
                                    <h5 class="col-5">Kembalian</h5>
                                    <input type="text" id="kembali" name="kembali" class="form-control col-7 text-end"
                                        value="0" readonly>
                                </div>
                                <div class="checkout">
                                    <button type="submit" class="btn btn-primary col-12 btn-simpan">Checkout</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end checkout -->
        </div>
        <!-- End kasir section -->

    </div>
    <!-- END Section layouts   -->

    @includeIf('sale.product')
@endsection

@push('scripts')
    <script>
        let table, table2;
        $(document).ready(function() {
            // console.log('ready');
            // $("#closed-sidebar-btn").click();
            // $(".tampilProdukCoba").click();
            $("#id_produk").focus();
        });

        $(function() {
            $("#submit-tambah-produk").submit(function(e) {
                e.preventDefault();
                tambahProduk();

            });

            table = $('.table-penjualan').DataTable({
                    responsive: true,
                    select: true,
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: {
                        url: '{{ route('transaksi.data', $id_penjualan) }}',
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            searchable: false,
                            sortable: false
                        },
                        {
                            data: 'kode_produk'
                        },
                        {
                            data: 'nama_produk'
                        },
                        {
                            data: 'harga_jual'
                        },
                        {
                            data: 'jumlah'
                        },
                        {
                            data: 'diskon'
                        },
                        {
                            data: 'subtotal'
                        },
                        {
                            data: 'aksi',
                            searchable: false,
                            sortable: false
                        },
                    ],
                    dom: 'rt',
                    bSort: false,
                    paginate: false,
                })
                .on('draw.dt', function() {
                    loadForm($('#diskon').val());
                    setTimeout(() => {
                        $('#dibayar').trigger('input');
                    }, 300);
                });
            table2 = $('.table-produk').DataTable();
            $(document).on('input', '.quantity', function() {
                let id = $(this).data('id');
                let jumlah = parseInt($(this).val());

                if (jumlah < 1) {
                    $(this).val(1);
                    alert('Jumlah tidak boleh kurang dari 1');
                    return;
                }
                if (jumlah > 10000) {
                    $(this).val(10000);
                    alert('Jumlah tidak boleh lebih dari 10000');
                    return;
                }

                $.post(`{{ url('/transaksi') }}/${id}`, {
                        '_token': $('[name=csrf-token]').attr('content'),
                        '_method': 'put',
                        'jumlah': jumlah
                    })
                    .done(response => {
                        table.ajax.reload(() => loadForm($('#diskon').val(), $('#dibayar').val()));
                    })
                    .fail(errors => {
                        alert('Tidak dapat mengubah data');
                        return;
                    });
            });

            $(document).on('input', '#diskon', function() {
                if ($(this).val() == "") {
                    $(this).val(0).select();
                }

                loadForm($(this).val(), $('#dibayar').val());
            }).focus(function() {
                $(this).select();
            });;

            $('#dibayar').on('input', function() {
                if ($(this).val() == "") {
                    $(this).val(0).select();
                }

                loadForm($('#diskon').val(), $(this).val());
            }).focus(function() {
                $(this).select();
            });

            $('.form-penjualan').submit(function(e) {
                if ($('.total_item').text() == 0) {
                    e.preventDefault();
                    alert('Tidak ada produk yang dipilih');
                    return;
                }

            });

            $(document).keydown(function(e) {
                if (e.key == 'End') {
                    $("#dibayar").focus();
                }
            });
        });

        function tampilProduk() {
            $('#modal-produk').modal('show');
        }

        function hideProduk() {
            $('#modal-produk').modal('hide');
        }

        function pilihProduk(id) {
            $('#id_produk').val(id);
            hideProduk();
            tambahProduk();
        }

        function tambahProduk() {
            idproduk = $("#id_produk").val()
            $.post('{{ route('transaksi.store') }}', $('.form-produk').serialize()).done(response => {
                $('#id_produk').focus();
                $("#id_produk").val("")
                table.ajax.reload(() => loadForm($('#diskon').val()));
            }).fail(errors => {
                $('#modal-produk').modal('show');
                $('input[type="search"]').val(idproduk);
                $('input[type="search"]').focus();
                setTimeout(() => {
                    $('input[type="search"]').focus();
                }, 300);

                $("#id_produk").val("");
                // alert('Tidak dapat menyimpan data');
                return;
            });
        }

        function deleteData(url) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, {
                        '_token': $("meta[name='csrf-token']").attr('content'),
                        '_method': 'delete'
                    })
                    .done((response) => {
                        table.ajax.reload(() => loadForm($('#diskon').val()));
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menghapus data');
                        return;
                    });
            }
        }

        function loadForm(diskon = 0, dibayar = 0) {
            $('#total').val($('.total').text());
            $('#total_item').val($('.total_item').text());
            $total = parseInt($('.total').text());
            diskon_produk = parseInt($('.diskon_produk').text()) + parseInt(diskon);

            $.get(`{{ url('/transaksi/loadform') }}/${diskon_produk}/${$('.total').text()}/${dibayar}`)
                .done(response => {
                    $('#subtotalrp').val('Rp. ' + response.subtotalrp);
                    $('#totalbayar').val('Rp. ' + (response.totalbayar));
                    $('#bayar').val(response.dibayar);

                    $('#kembali').val('Rp.' + response.kembalirp);
                    if ($('#dibayar').val() < $total - diskon) {
                        $('.btn-simpan').attr('disabled', true);
                    } else {
                        $('.btn-simpan').attr('disabled', false);
                    }
                })
                .fail(errors => {
                    alert('Tidak dapat menampilkan data');
                    return;
                })
        }
    </script>
@endpush
