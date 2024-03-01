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
                    <div>Pembelian
                        <div class="page-title-subheading">
                            Daftar Pembelian Barang
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 text-center align-self-center">
                <a href="{{ url('/pembelian/create') }}">
                    <button class="btn btn-primary rounded-pill px-3" id="addProduct">Tambah</button>
                </a>
            </div>
        </div>
        <!-- END TITLE -->

        <!-- CARD DASHBOARD -->
        <div class="row">
            <!-- total pendapatan -->
            <div class="col-sm-12 col-md-4 col-xl-3 p-3">
                <div class="card mb-3 widget-content">
                    <div class="content">
                        <div class="widget-content-left mb-2">
                            <i class="pe-7s-cash col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Total Transaksi Pembelian</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2" id="countPurchase">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END CARD DASHBOARD -->

        <div class="pembelian__section">
            <!-- Barang -->
            <div class="pembelian__content">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title text-center font-size-xlg">Pembelian</h5>
                        <table class="mb-0 table" id="tablePurchase">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID Transaksi</th>
                                    <th>Supplier</th>
                                    <th>Total Barang</th>
                                    <th>Total Pembelian</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tablePurchaseBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end Barang section -->
    </div>
    <!-- END Section layouts   -->
@endsection


@section('modal')
    {{-- Modal --}}
    <div class="modal fade modalEdit" id="modalEdit" role="dialog" tabindex="-1" aria-labelledby="modalEditLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    {{-- End modal --}}
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            initializeDataTable('tablePurchase');

            getPurchase();
        });

        const getPurchase = (id) => {
            $('#tablePurchase').DataTable().clear().draw();
            $('#tablePurchaseBody').html(tableLoader(7,
                `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                url: `{{ url('pembelian/data') }}`,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    $('#countPurchase').html(response.data.purchases.length);
                    if (response.data.purchases.length > 0) {
                        response.data.purchases.forEach((purchase, index) => {
                            $('#tablePurchase').DataTable().row.add([
                                index + 1,
                                purchase.id,
                                purchase.supplier.Nama,
                                purchase.total,
                                formatCurrency(purchase.amount),
                                moment(purchase.created_at).format('DD-MM-Y'),
                                `<button class="btn btn-danger rounded-circle px-2" onclick="deletePurchase('${purchase.id}','${purchase.supplier.Nama}')"><i class="bi bi-trash"></i></button>
                                    <a href="{{ url('pembelian/detail/${purchase.id}/create') }}" class="btn btn-primary rounded-circle px-2"><i class="bi bi-pencil"></i></a>`
                            ]).draw(false).node();
                        });
                    } else {
                        $('#tablePurchaseBody').html(tableEmpty(7,
                            'barang'));
                    }
                },
                error: function(error) {
                    $('#tablePurchaseBody').html(tableEmpty(7,
                        'barang'));
                }
            });
        }

        const initSelect2Supplier = (supplier) => {
            $('#supplier_id').select2({
                dropdownParent: $('#modalEdit'),
                theme: "bootstrap-5",
                placeholder: 'Masukkan Merk Barang',
                width: '100%',
                allowClear: true,
                minimumInputLength: 1, // Minimum characters required to start searching
                language: {
                    inputTooShort: function(args) {
                        var remainingChars = args.minimum - args.input.length;
                        return "Masukkan kata kunci setidaknya " + remainingChars +
                            " karakter";
                    },
                    searching: function() {
                        return "Sedang mengambil data...";
                    },
                    noResults: function() {
                        return "Merk tidak ditemukan";
                    },
                    errorLoading: function() {
                        return "Terjadi kesalahan saat memuat data";
                    },
                },
                templateSelection: function(data, container) {
                    if (data.id === '') {
                        return data.text;
                    }
                    var match = data.text.match(/^(.*?) \(/);
                    var resultName = match[1];

                    return $('<span class="custom-selection">' + resultName + '</span>');
                },
                ajax: {
                    url: "{{ route('supplier.search.data') }}", // URL to fetch data from
                    dataType: 'json', // Data type expected from the server
                    processResults: function(response) {
                        var suppliers = response.data.suppliers;
                        var options = [];

                        suppliers.forEach(function(supplier) {
                            options.push({
                                id: supplier.idSupplier, // Use the supplier
                                text: supplier.Nama + ' (' + supplier.Produk +
                                    ')'
                            });
                        });

                        return {
                            results: options // Processed results with id and text properties
                        };
                    },
                    cache: true, // Cache the results for better performance
                }
            })
            // Periksa apakah nilai id sudah ada dalam opsi saat ini
            if ($('#supplier_id').find('option[value="' + supplier.id + '"]').length === 0) {
                // Jika tidak, tambahkan elemen <option> baru
                var $newOption = new Option(`${supplier.Nama} (${supplier.Produk})`, supplier.id, true, true);
                $('#supplier_id').append($newOption).trigger('change');
            } else {
                // Jika sudah ada, langsung atur nilai dan perbarui tampilan
                $('#supplier_id').val(id).trigger('change');
            }
        }

        const deletePurchase = (id, name) => {
            Swal.fire({
                title: 'Hapus Produk',
                text: `Apakah Anda yakin ingin menghapus ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: `{{ url('pembelian/${id}') }}`,
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Hapus Produk Berhasil',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            getPurchase();
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            if (xhr.responseJSON) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: `Hapus Produk Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`,
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                            }
                            return false;
                        },
                    });
                }
            })
        }
    </script>
@endpush
