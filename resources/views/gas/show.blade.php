@extends('layouts.main')

@section('content')
    <div class="app-main__inner">
        <!-- TITLE -->
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-note2 icon-gradient bg-plum-plate">
                        </i>
                    </div>
                    <div>Gas
                        <div class="page-title-subheading">
                            Daftar Gas
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END TITLE -->
        <!-- CARD DASHBOARD -->
        <div class="row">
            <!-- jumlah gas -->
            <div class="col-sm-4 col-md-4 col-xl-3 p-3">
                <div class="card mb-3 widget-content">
                    <div class="content">
                        <div class="widget-content-left mb-2">
                            <i class="pe-7s-cash col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Jumlah Gas</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="countGas">{{ $gas->stok }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- end Jumlah Gas --}}
            <!-- Jumlah Gas Isi Tersisa -->
            <div class="col-sm-4 col-md-4 col-xl-3 p-3">
                <div class="card mb-3 widget-content">
                    <div class="content">
                        <div class="widget-content-left mb-2">
                            <i class="pe-7s-cash col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Jumlah Gas Isi Tersisa</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="remainingGas">-</span></div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- end Jumlah Gas Isi Tersisa --}}
            <!-- Jumlah Gas Kuota Tersisa -->
            <div class="col-sm-4 col-md-4 col-xl-3 p-3">
                <div class="card mb-3 widget-content">
                    <div class="content">
                        <div class="widget-content-left mb-2">
                            <i class="pe-7s-cash col-2" style="font-size: 30px;"></i>
                            <div class="widget-heading col-10 widget__title">Jumlah Gas Kuota Tersisa</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers mb-2"><span id="remainingQuota">-</span></div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- end Jumlah Gas Kuota Tersisa --}}
        </div>
        <!-- END CARD DASHBOARD -->

        <!-- Kuota Pelanggan Gas -->
        <div class="GasCustomerQuotaSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Daftar Kuota Gas Pelanggan</h5>
                    <table class="display nowrap" style="width:100%" id="tableGasCustomerQuota">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pelanggan</th>
                                <th>Kuota</th>
                                <th>Total Bayar</th>
                                <th>Total Tabung</th>
                                <th>Total Ambil</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableGasCustomerQuotaBody">
                        </tbody>
                        <tfoot class="table-striped-footer">
                            <tr>
                                <th colspan="2">Total</th>
                                <th id="totalQuota"></th>
                                <th id="totalPayGas"></th>
                                <th id="totalEmptyGas"></th>
                                <th id="totalTakeGas"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="col">
                        <p class="mb-1">Menambah data pelanggan penerima kuota gas</p>
                        <button onclick="showAddGasCustomerQuota()" class="btn btn-primary">Tambah Pelanggan</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end Kuota Pelanggan Gas -->

        <!-- Transaksi Gas -->
        <div class="gasTransactionSection">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-center">Daftar Penjualan Gas</h5>
                    <table class="display nowrap" style="width:100%" id="tableGasTransaction">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Pembelian</th>
                                <th>Nama Pelanggan</th>
                                <th>Bayar</th>
                                <th>Tabung</th>
                                <th>Ambil</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableGasTransactionBody">
                        </tbody>
                        <tfoot class="table-striped-footer">
                            <tr>
                                <th colspan="3">Total</th>
                                <th id="totalPayGas"></th>
                                <th id="totalEmptyGas"></th>
                                <th id="totalTakeGas"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="col">
                        <p class="mb-1">Menambah Transaksi Gas</p>
                        <button onclick="showAddGasTransaction()" class="btn btn-primary">Tambah Transaksi</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end Transaksi Gas -->
    </div>
@endsection

@push('scripts')
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 1500
            })
        </script>
    @endif
    <script>
        $(document).ready(function() {
            var config =
                initializeDataTable('tableGasCustomerQuota', {
                    "columnDefs": [{
                        "targets": [0, 1, 2, 3],
                        "className": "text-center"
                    }],
                    "footerCallback": function(row, data, start, end, display) {
                        var api = this.api(),
                            data;

                        // Remove the formatting to get integer data for summation
                        var intVal = function(i) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '') * 1 :
                                typeof i === 'number' ?
                                i : 0;
                        };

                        // Total over all pages
                        totalQuota = api
                            .column(2, {
                                page: 'current'
                            })
                            .data()
                            .sum();

                        totalPayGas = api
                            .column(4, {
                                page: 'current'
                            })
                            .data()
                            .sum();

                        totalEmptyGas = api
                            .column(4, {
                                page: 'current'
                            })
                            .data()
                            .sum();

                        totalTakeGas = api
                            .column(5, {
                                page: 'current'
                            })
                            .data()
                            .sum();

                        // Update footer
                        $(api.column(2).footer()).html(totalQuota);
                        $(api.column(3).footer()).html(totalPayGas);
                        $(api.column(4).footer()).html(totalEmptyGas);
                        $(api.column(5).footer()).html(totalTakeGas);
                    }
                })
            initializeDataTable('tableGasTransaction', {
                "columnDefs": [{
                    "targets": [0, 1, 2, 3],
                    "className": "text-center"
                }],
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api(),
                        data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };

                    // Total over all pages
                    totalPayGas = api
                        .column(3, {
                            page: 'current'
                        })
                        .data()
                        .sum();

                    totalEmptyGas = api
                        .column(4, {
                            page: 'current'
                        })
                        .data()
                        .sum();

                    totalTakeGas = api
                        .column(5, {
                            page: 'current'
                        })
                        .data()
                        .sum();

                    // Update footer
                    $(api.column(3).footer()).html(totalPayGas);
                    $(api.column(4).footer()).html(totalEmptyGas);
                    $(api.column(5).footer()).html(totalTakeGas);
                }
            })

            getGasRemaining();
            getGasCustomerQuota();
            getGasTransaction();
        });

        const getGasRemaining = () => {
            $.ajax({
                type: "GET",
                url: `{{ url('gas/' . $gas->id . '/tersisa/data') }}`,
                dataType: "json",
                success: function(response) {
                    $('#remainingGas').text(response.data.remainingGas);
                    $('#remainingQuota').text(response.data.remainingQuota);
                }
            });
        }

        const getGasCustomerQuota = () => {
            $('#tableGasCustomerQuota').DataTable().clear().draw();
            $('#tableGasCustomerQuotaBody').html(tableLoader(8, `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                type: "GET",
                url: `{{ url('gas-pelanggan/' . $gas->id . '/data') }}`,
                dataType: "json",
                success: function(response) {
                    if (response.data.gasCustomers.length > 0) {
                        $.each(response.data.gasCustomers, function(index, gasCustomer) {
                            var rowData = [
                                index + 1,
                                gasCustomer.customer.nama,
                                gasCustomer.kuota,
                                gasCustomer.gas_transactions.length > 0 ? gasCustomer
                                .gas_transactions[0].total_bayar_tabung : 0,
                                gasCustomer.gas_transactions.length > 0 ? gasCustomer
                                .gas_transactions[0].total_tabung_kosong : 0,
                                gasCustomer.gas_transactions.length > 0 ? gasCustomer
                                .gas_transactions[0].total_ambil_tabung : 0,
                                `<button class="btn btn-sm btn-primary" onclick='showEditGasCustomerQuota(${JSON.stringify(gasCustomer)})'><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteGasCustomerQuota('${gasCustomer.id}')"><i class="bi bi-trash"></i></button>`
                            ];
                            var rowNode = $('#tableGasCustomerQuota').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();

                            // $(rowNode).find('td').eq(0).addClass('text-center');
                            // $(rowNode).find('td').eq(4).addClass('text-center text-nowrap');
                        });
                    } else {
                        $('#tableGasCustomerQuotaBody').html(tableEmpty(8,
                            'barang'));
                    }
                }
            });
        }

        const showAddGasCustomerQuota = () => {
            // mengambil data sisa gas dan kuota
            let remainingQuota = parseInt($('#remainingQuota').text());

            Swal.fire({
                title: "Tambah Kuota Gas Pelanggan",
                html: `
                <form id="formAddGasCustomerQuota">
                    @csrf
                    <div class="">
                        <label for="customerId" class="col-form-label">Nama Pelanggan</label>
                        <div class="">
                            <div class="select2-input select2-info" style="width: 100%">
                                <select id="customerId" name="customerId" class="form-control rounded__10">
                                    <option value="">&nbsp;</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <label for="quota" class="col-form-label">Jumlah Gas</label>
                        <div class="">
                            <input required type="number" class="form-control rounded__10 " id="quota" name="quota" min="0" max="${remainingQuota}">
                        </div>
                    </div>
                </form>
                `,
                showCancelButton: true,
                confirmButtonText: "Tambah",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-danger"
                },
                didOpen: () => {
                    // Add event listener for enter key
                    const form = document.getElementById('formAddGasCustomerQuota');
                    form.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault(); // Prevent form submission
                            Swal.getConfirmButton().click(); // Trigger the confirm button
                        }
                    });

                    initializeCustomerSelect2('customerId', "{{ route('customer.data') }}", (e) => {},
                        processResultHandler = (response) => {
                            var customers = response.data.customers;
                            var options = [];

                            customers.forEach(function(customer) {
                                options.push({
                                    id: customer.id, // Use the customer id
                                    text: customer.nama + ' (' + customer.nik + ')'
                                });
                            });

                            return {
                                results: options // Processed results with id and text properties
                            };
                        });

                    enforceMaxValue('quota');
                },
                preConfirm: () => {
                    const customerId = Swal.getPopup().querySelector('#customerId').value;
                    const quota = Swal.getPopup().querySelector('#quota').value;

                    if (!customerId) {
                        Swal.showValidationMessage(`Data pelanggan tidak boleh kosong`);
                    }

                    if (!quota) {
                        Swal.showValidationMessage(`Tanggal tidak boleh kosong`);
                    }

                    return {
                        customerId: customerId,
                        quota: quota,
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: `{{ url('gas-pelanggan') }}`,
                        data: {
                            _token: "{{ csrf_token() }}",
                            gasId: "{{ $gas->id }}",
                            customerId: result.value.customerId,
                            quota: result.value.quota,
                        },
                        success: function(response) {
                            Swal.fire({
                                    title: "Berhasil!",
                                    text: response.meta.message,
                                    icon: "success",
                                    showCancelButton: false,
                                    confirmButtonText: "Okay",
                                    customClass: {
                                        confirmButton: "btn btn-success"
                                    },
                                })
                                .then(() => {
                                    getGasRemaining();
                                    getGasCustomerQuota()
                                });
                        },
                        error: function(xhr, status, error) {
                            if (xhr.responseJSON) {
                                errorAlert("Gagal!",
                                    `Ubah data gas Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`
                                );
                            } else {
                                errorAlert("Gagal!",
                                    `Terjadi kesalahan pada server. Error: ${xhr.responseText}`
                                );
                            }
                            return false;
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Batal",
                        text: "Ubah data gas dibatalkan",
                        icon: "info",
                        showCancelButton: false,
                        confirmButtonText: "Okay",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        },
                    });
                }
            })
        }

        const showEditGasCustomerQuota = (gasCustomer) => {
            // mengambil data sisa gas dan kuota
            let remainingQuota = parseInt($('#remainingQuota').text());

            Swal.fire({
                title: "Ubah Kuota Gas Pelanggan",
                html: `
                <form id="formEditGasCustomerQuota">
                    @csrf
                    <div class="">
                        <label for="customerIdUpdate" class="col-form-label">Nama Pelanggan</label>
                        <div class="">
                            <div class="select2-input select2-info" style="width: 100%">
                                <select id="customerIdUpdate" name="customerIdUpdate" class="form-control rounded__10">
                                    <option>${gasCustomer.customer.nama}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <label for="quotaUpdate" class="col-form-label">Jumlah Gas</label>
                        <div class="">
                            <input required type="number" class="form-control rounded__10 " id="quotaUpdate" name="quotaUpdate" min="0" max="${remainingQuota + gasCustomer.kuota}" value="${gasCustomer.kuota}">
                        </div>
                    </div>
                </form>
                `,
                showCancelButton: true,
                confirmButtonText: "Ubah",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-danger"
                },
                didOpen: () => {
                    // Add event listener for enter key
                    const form = document.getElementById('formEditGasCustomerQuota');
                    form.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault(); // Prevent form submission
                            Swal.getConfirmButton().click(); // Trigger the confirm button
                        }
                    });

                    enforceMaxValue('quotaUpdate');
                },
                preConfirm: () => {
                    const quotaUpdate = Swal.getPopup().querySelector('#quotaUpdate').value;

                    if (!quotaUpdate) {
                        Swal.showValidationMessage(`Tanggal tidak boleh kosong`);
                    }

                    return {
                        quotaUpdate: quotaUpdate,
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: `{{ url('gas-pelanggan/${gasCustomer.id}') }}`,
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "PUT",
                            quotaUpdate: result.value.quotaUpdate,
                        },
                        success: function(response) {
                            Swal.fire({
                                    title: "Berhasil!",
                                    text: response.meta.message,
                                    icon: "success",
                                    showCancelButton: false,
                                    confirmButtonText: "Okay",
                                    customClass: {
                                        confirmButton: "btn btn-success"
                                    },
                                })
                                .then(() => {
                                    getGasRemaining();
                                    getGasCustomerQuota()
                                });
                        },
                        error: function(xhr, status, error) {
                            if (xhr.responseJSON) {
                                errorAlert("Gagal!",
                                    `Ubah data gas Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`
                                );
                            } else {
                                errorAlert("Gagal!",
                                    `Terjadi kesalahan pada server. Error: ${xhr.responseText}`
                                );
                            }
                            return false;
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Batal",
                        text: "Ubah data gas dibatalkan",
                        icon: "info",
                        showCancelButton: false,
                        confirmButtonText: "Okay",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        },
                    });
                }
            })
        }

        const getGasTransaction = () => {
            $('#tableGasTransaction').DataTable().clear().draw();
            $('#tableGasTransactionBody').html(tableLoader(8, `{{ asset('assets/svg/Ellipsis-2s-48px.svg') }}`));

            $.ajax({
                type: "GET",
                url: `{{ url('gas-transaksi/' . $gas->id . '/data') }}`,
                dataType: "json",
                success: function(response) {
                    if (response.data.gasTransactions.length > 0) {
                        $.each(response.data.gasTransactions, function(index, gasTransaction) {
                            var rowData = [
                                index + 1,
                                moment(gasTransaction.created_at).format('DD-MM-YYYY'),
                                gasTransaction.gas_customer.customer.nama,
                                gasTransaction.bayar_tabung,
                                gasTransaction.tabung_kosong,
                                gasTransaction.ambil_tabung,
                                `<button class="btn btn-sm btn-primary" onclick='showEditGasTransaction(${JSON.stringify(gasTransaction)})'><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteGasTransaction('${gasTransaction.id}')"><i class="bi bi-trash"></i></button>`
                            ];
                            var rowNode = $('#tableGasTransaction').DataTable().row.add(rowData)
                                .draw(
                                    false)
                                .node();

                            // $(rowNode).find('td').eq(0).addClass('text-center');
                            // $(rowNode).find('td').eq(4).addClass('text-center text-nowrap');
                        });
                    } else {
                        $('#tableGasTransactionBody').html(tableEmpty(8,
                            'barang'));
                    }
                }
            });
        }

        const showAddGasTransaction = () => {
            Swal.fire({
                title: "Tambah Pembelian Gas Pelanggan",
                html: `
                <form id="formAddGasTransaction">
                    @csrf
                    <div class="">
                        <label for="gasCustomerId" class="col-form-label">Nama Pelanggan</label>
                        <div class="">
                            <div class="select2-input select2-info" style="width: 100%">
                                <select id="gasCustomerId" name="gasCustomerId" class="form-control rounded__10">
                                    <option value="">&nbsp;</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <label for="payGas" class="col-form-label">Bayar Gas</label>
                        <div class="">
                            <input type="number" disabled class="form-control rounded__10 " id="payGas" name="payGas" value="0" min="0">
                        </div>
                    </div>
                    <div class="">
                        <label for="emptyGas" class="col-form-label">Tabung Kosong</label>
                        <div class="">
                            <input type="number" disabled class="form-control rounded__10 " id="emptyGas" name="emptyGas" value="0" min="0">
                        </div>
                    </div>
                    <div class="">
                        <label for="takeGas" class="col-form-label">Ambil Gas</label>
                        <div class="">
                            <input type="number" disabled class="form-control rounded__10 " id="takeGas" name="takeGas" value="0" min="0">
                        </div>
                    </div>
                </form>
                `,
                showCancelButton: true,
                confirmButtonText: "Tambah",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-danger"
                },
                didOpen: () => {
                    // Add event listener for enter key
                    const form = document.getElementById('formAddGasTransaction');
                    form.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault(); // Prevent form submission
                            Swal.getConfirmButton().click(); // Trigger the confirm button
                        }
                    });

                    initializeCustomerSelect2('gasCustomerId',
                        "{{ url('gas-pelanggan/' . $gas->id . '/data') }}",
                        function(e) {
                            $(this).removeClass('is-invalid');
                            // mengaktifkan inputan
                            $('#payGas').attr('disabled', false);
                            $('#emptyGas').attr('disabled', false);
                            $('#takeGas').attr('disabled', false);

                            // mengatur nilai maksimal inputan
                            $('#payGas').attr('max', (e.params.data.quota - e.params.data.payGas) < 0 ?
                                0 : (e.params.data.quota - e.params.data.payGas));
                            $('#emptyGas').attr('max', (e.params.data.quota - e.params.data.emptyGas) <
                                0 ?
                                0 : (e.params.data.quota - e.params.data.emptyGas));
                            $('#takeGas').attr('max', e.params.data.quota - e.params.data.takeGas);
                        }, (response) => {
                            var gasCustomers = response.data.gasCustomers;
                            var options = [];

                            gasCustomers.forEach(function(gasCustomer) {
                                options.push({
                                    id: gasCustomer.id, // Use the gasCustomer id
                                    text: gasCustomer.customer.nama + ' (' + gasCustomer
                                        .customer
                                        .nik + ')',
                                    payGas: gasCustomer.gas_transactions.length > 0 ?
                                        gasCustomer
                                        .gas_transactions[0].total_bayar_tabung : 0,
                                    emptyGas: gasCustomer.gas_transactions.length > 0 ?
                                        gasCustomer
                                        .gas_transactions[0].total_tabung_kosong : 0,
                                    takeGas: gasCustomer.gas_transactions.length > 0 ?
                                        gasCustomer
                                        .gas_transactions[0].total_ambil_tabung : 0,
                                    quota: gasCustomer.kuota,
                                });
                            });

                            return {
                                results: options // Processed results with id and text properties
                            };
                        });

                    enforceMaxValue('payGas');
                    enforceMaxValue('emptyGas');
                    enforceMaxValue('takeGas');
                },
                preConfirm: () => {
                    const gasCustomerId = Swal.getPopup().querySelector('#gasCustomerId')
                        .value;
                    const payGas = Swal.getPopup().querySelector('#payGas').value;
                    const emptyGas = Swal.getPopup().querySelector('#emptyGas').value;
                    const takeGas = Swal.getPopup().querySelector('#takeGas').value;

                    if (!gasCustomerId) {
                        Swal.showValidationMessage(`Data pelanggan tidak boleh kosong`);
                    }

                    return {
                        gasCustomerId: gasCustomerId,
                        payGas: payGas,
                        emptyGas: emptyGas,
                        takeGas: takeGas,
                    }
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Tambah Pembelian Gas Pelanggan',
                        text: 'Sedang menambah data pembelian gas pelanggan...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading();
                        },
                    });
                    $.ajax({
                        type: "POST",
                        url: `{{ url('gas-transaksi') }}`,
                        data: {
                            _token: "{{ csrf_token() }}",
                            gasId: "{{ $gas->id }}",
                            gasCustomerId: result.value.gasCustomerId,
                            payGas: result.value.payGas,
                            emptyGas: result.value.emptyGas,
                            takeGas: result.value.takeGas,
                        },
                        success: function(response) {
                            Swal.fire({
                                    title: "Berhasil!",
                                    text: response.meta.message,
                                    icon: "success",
                                    showCancelButton: false,
                                    confirmButtonText: "Okay",
                                    customClass: {
                                        confirmButton: "btn btn-success"
                                    },
                                })
                                .then(() => {
                                    getGasRemaining();
                                    getGasTransaction()
                                    getGasCustomerQuota()
                                });
                        },
                        error: function(xhr, status, error) {
                            if (xhr.responseJSON) {
                                errorAlert("Gagal!",
                                    `Transaksi Gas Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`
                                );
                            } else {
                                errorAlert("Gagal!",
                                    `Terjadi kesalahan pada server. Error: ${xhr.responseText}`
                                );
                            }
                            return false;
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Batal",
                        text: "Transaksi Gas dibatalkan",
                        icon: "info",
                        showCancelButton: false,
                        confirmButtonText: "Okay",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        },
                    });
                }
            })
        }

        const showEditGasTransaction = async (gasTransaction) => {
            const responseGasCustomer = await $.ajax({
                type: "GET",
                url: `{{ url('gas-pelanggan/' . $gas->id . '/data') }}`,
                dataType: "json",
                data: {
                    filterName: gasTransaction.gas_customer.customer.nama
                }
            });

            const gasCustomer = responseGasCustomer.data.gasCustomers.find(gasCustomer => gasCustomer.id ===
                gasTransaction
                .gas_pelanggan_id);
            let remainingPayGas = gasTransaction.bayar_tabung + gasCustomer.kuota - gasCustomer.gas_transactions[0]
                .total_bayar_tabung;
            let remainingEmptyGas = gasTransaction.tabung_kosong + gasCustomer.kuota - gasCustomer.gas_transactions[
                0].total_tabung_kosong;
            let remainingTakeGas = gasTransaction.ambil_tabung + gasCustomer.kuota - gasCustomer.gas_transactions[0]
                .total_ambil_tabung;

            Swal.fire({
                title: "Ubah Pembelian Gas Pelanggan",
                html: `
                <form id="formAddGasTransaction">
                    @csrf
                    <div class="">
                        <label for="gasCustomerIdUpdate" class="col-form-label">Nama Pelanggan</label>
                        <div class="">
                            <div class="select2-input select2-info" style="width: 100%">
                                <select id="gasCustomerIdUpdate" name="gasCustomerIdUpdate" class="form-control rounded__10">
                                    <option value="${gasTransaction.gas_pelanggan_id}">${gasTransaction.gas_customer.customer.nama}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <label for="payGasUpdate" class="col-form-label">Bayar Gas</label>
                        <div class="">
                            <input type="number" class="form-control rounded__10 " id="payGasUpdate" name="payGasUpdate" min="0" value="${gasTransaction.bayar_tabung}" max="${remainingPayGas < 0 ? 0:remainingPayGas}">
                        </div>
                    </div>
                    <div class="">
                        <label for="emptyGasUpdate" class="col-form-label">Tabung Kosong</label>
                        <div class="">
                            <input type="number" class="form-control rounded__10 " id="emptyGasUpdate" name="emptyGasUpdate" min="0" value="${gasTransaction.tabung_kosong}" max="${remainingEmptyGas < 0 ? 0:remainingEmptyGas}">
                        </div>
                    </div>
                    <div class="">
                        <label for="takeGasUpdate" class="col-form-label">Ambil Gas</label>
                        <div class="">
                            <input type="number" class="form-control rounded__10 " id="takeGasUpdate" name="takeGasUpdate" min="0" value="${gasTransaction.ambil_tabung}" max="${remainingTakeGas < 0 ? 0:remainingTakeGas}">
                        </div>
                    </div>
                </form>
                `,
                showCancelButton: true,
                confirmButtonText: "Ubah",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-danger"
                },
                didOpen: () => {
                    // Add event listener for enter key
                    const form = document.getElementById('formAddGasTransaction');
                    form.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault(); // Prevent form submission
                            Swal.getConfirmButton().click(); // Trigger the confirm button
                        }
                    });

                    initializeCustomerSelect2('gasCustomerIdUpdate',
                        "{{ url('gas-pelanggan/' . $gas->id . '/data') }}",
                        (e) => {
                            $(this).removeClass('is-invalid');
                            // mengaktifkan inputan
                            $('#payGasUpdate').val(0);
                            $('#emptyGasUpdate').val(0);
                            $('#takeGasUpdate').val(0);

                            // mengatur nilai maksimal inputan
                            $('#payGasUpdate').attr('max', (e.params.data.quota - e.params.data
                                    .payGasUpdate) < 0 ?
                                0 : (e.params.data.quota - e.params.data.payGasUpdate));
                            $('#emptyGasUpdate').attr('max', (e.params.data.quota - e.params.data
                                    .emptyGasUpdate) <
                                0 ?
                                0 : (e.params.data.quota - e.params.data.emptyGasUpdate));
                            $('#takeGasUpdate').attr('max', e.params.data.quota - e.params.data
                                .takeGasUpdate);
                        }, (response) => {
                            var gasCustomers = response.data.gasCustomers;
                            var options = [];

                            gasCustomers.forEach(function(gasCustomer) {
                                options.push({
                                    id: gasCustomer.id, // Use the gasCustomer id
                                    text: gasCustomer.customer.nama + ' (' +
                                        gasCustomer
                                        .customer
                                        .nik + ')',
                                    payGasUpdate: gasCustomer.gas_transactions
                                        .length >
                                        0 ?
                                        gasCustomer
                                        .gas_transactions[0].total_bayar_tabung : 0,
                                    emptyGasUpdate: gasCustomer.gas_transactions
                                        .length > 0 ?
                                        gasCustomer
                                        .gas_transactions[0].total_tabung_kosong :
                                        0,
                                    takeGasUpdate: gasCustomer.gas_transactions
                                        .length >
                                        0 ?
                                        gasCustomer
                                        .gas_transactions[0].total_ambil_tabung : 0,
                                    quota: gasCustomer.kuota,
                                });
                            });

                            return {
                                results: options // Processed results with id and text properties
                            };
                        });

                    enforceMaxValue('payGasUpdate');
                    enforceMaxValue('emptyGasUpdate');
                    enforceMaxValue('takeGasUpdate');
                },
                preConfirm: () => {
                    const gasCustomerIdUpdate = Swal.getPopup().querySelector('#gasCustomerIdUpdate')
                        .value;
                    const payGasUpdate = Swal.getPopup().querySelector('#payGasUpdate').value;
                    const emptyGasUpdate = Swal.getPopup().querySelector('#emptyGasUpdate').value;
                    const takeGasUpdate = Swal.getPopup().querySelector('#takeGasUpdate').value;

                    if (!gasCustomerIdUpdate) {
                        Swal.showValidationMessage(`Data pelanggan tidak boleh kosong`);
                    }

                    return {
                        gasCustomerIdUpdate: gasCustomerIdUpdate,
                        payGasUpdate: payGasUpdate,
                        emptyGasUpdate: emptyGasUpdate,
                        takeGasUpdate: takeGasUpdate,
                    }
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Ubah Pembelian Gas Pelanggan',
                        text: 'Sedang menambah data pembelian gas pelanggan...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading();
                        },
                    });
                    $.ajax({
                        type: "POST",
                        url: `{{ url('gas-transaksi/${gasTransaction.id}') }}`,
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "PUT",
                            gasCustomerIdUpdate: result.value.gasCustomerIdUpdate,
                            payGasUpdate: result.value.payGasUpdate,
                            emptyGasUpdate: result.value.emptyGasUpdate,
                            takeGasUpdate: result.value.takeGasUpdate,
                        },
                        success: function(response) {
                            Swal.fire({
                                    title: "Berhasil!",
                                    text: response.meta.message,
                                    icon: "success",
                                    showCancelButton: false,
                                    confirmButtonText: "Okay",
                                    customClass: {
                                        confirmButton: "btn btn-success"
                                    },
                                })
                                .then(() => {
                                    getGasRemaining();
                                    getGasTransaction()
                                    getGasCustomerQuota()
                                });
                        },
                        error: function(xhr, status, error) {
                            if (xhr.responseJSON) {
                                errorAlert("Gagal!",
                                    `Transaksi Gas Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`
                                );
                            } else {
                                errorAlert("Gagal!",
                                    `Terjadi kesalahan pada server. Error: ${xhr.responseText}`
                                );
                            }
                            return false;
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Batal",
                        text: "Transaksi Gas dibatalkan",
                        icon: "info",
                        showCancelButton: false,
                        confirmButtonText: "Okay",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        },
                    });
                }
            })
        }

        const initializeCustomerSelect2 = (formId, ajaxUrl, selectHandler = () => {}, processResultHandler) => {
            $(`#${formId}`).select2({
                dropdownParent: $('.swal2-popup'),
                allowClear: true,
                theme: "bootstrap-5",
                placeholder: 'Masukkan nama pelanggan',
                width: '100%',
                minimumInputLength: 1, // Minimum characters required to start searching
                language: {
                    inputTooShort: function(args) {
                        var remainingChars = args.minimum - args.input.length;
                        return "Masukkan kata kunci setidaknya " + remainingChars + " karakter";
                    },
                    searching: function() {
                        return "Sedang mengambil data...";
                    },
                    noResults: function() {
                        return "Pelanggan tidak ditemukan";
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
                    if (match) {
                        var resultName = match[1];
                        return $('<span class="custom-selection">' + resultName + '</span>');
                    }
                    return data.text;
                },
                ajax: {
                    url: ajaxUrl, // URL to fetch data from
                    dataType: 'json', // Data type expected from the server
                    delay: 100, // Delay in milliseconds before the request is made
                    data: function(params) {
                        return {
                            filterName: params.term // Change 'term' to 'filterName',
                        };
                    },
                    processResults: processResultHandler,
                    cache: true, // Cache the results for better performance
                }
            }).on('select2:select', selectHandler);
        }

        // Add event listeners to number inputs to enforce max value
        const enforceMaxValue = (inputId) => {
            const input = document.getElementById(inputId);
            const label = document.querySelector(`label[for="${inputId}"]`);
            input.addEventListener('input', () => {
                const inputValue = parseInt(input.value);
                const inputMax = parseInt(input.max);
                if (inputValue > inputMax) {
                    input.value = input.max;
                    Swal.showValidationMessage(
                        `${label.textContent} tidak boleh lebih dari ${input.max}`
                    );
                }
            });
        };

        const deleteGasCustomerQuota = (gasCustomerId) => {
            Swal.fire({
                title: 'Hapus data pelanggan gas',
                text: `Apakah Anda yakin ingin menghapus data pelanggan gas?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Hapus Data Pelanggan Gas',
                        text: 'Sedang menghapus data pelanggan gas...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    $.ajax({
                        type: "DELETE",
                        url: `{{ url('gas-pelanggan/${gasCustomerId}') }}`,
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Hapus Pelanggan Gas Berhasil',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            getGasTransaction();
                            getGasCustomerQuota();
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            if (xhr.responseJSON) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: `Hapus Pelanggan Gas Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`,
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

        const deleteGasTransaction = (transactionId) => {
            Swal.fire({
                title: 'Hapus data transaksi gas',
                text: `Apakah Anda yakin ingin menghapus data transaksi gas?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Hapus Data Transaksi Gas',
                        text: 'Sedang menghapus data transaksi gas...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    $.ajax({
                        type: "DELETE",
                        url: `{{ url('gas-transaksi/${transactionId}') }}`,
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Hapus Transaksi Gas Berhasil',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            getGasTransaction();
                            getGasCustomerQuota();
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            if (xhr.responseJSON) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: `Hapus Transaksi Gas Gagal. ${xhr.responseJSON.meta.message} Error: ${xhr.responseJSON.data.error}`,
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
