const calculatePercentage = (index, data) => {
    const total = data.reduce((acc, value) => acc + value, 0);
    const percentages = ((data[index] / total) * 100).toFixed(2);

    return parseFloat(percentages);
};

const formatDecimalNumber = (num, fix) => {
    const number = parseFloat(num);
    if (isNaN(number)) {
        throw new Error(
            "Input must be a valid number or a string that can be parsed to a number."
        );
    }

    return number.toFixed(fix);
};

// INIT DATATABLE DASHBOARD
const initializeDashboardDataTable = (tableId) => {
    $(tableId).DataTable({
        columnDefs: [
            {
                targets: "filter-none",
                orderable: false,
            },
        ],
        language: {
            sEmptyTable: "Tidak ada data pegawai yang tersedia di tabel",
            sInfo: "Menampilkan data _START_ s.d _END_ dari _TOTAL_ total data",
            sInfoEmpty: "Menampilkan 0 dari 0 total data",
            sInfoFiltered: "(disaring dari total _MAX_ entri)",
            sInfoPostFix: "",
            sInfoThousands: ",",
            sLengthMenu: "_MENU_",
            sLoadingRecords: "Memuat...",
            sProcessing: "Memproses...",
            sSearch: "",
            searchPlaceholder: "Search...",
            sZeroRecords: "Tidak ditemukan data pegawai yang cocok",
            oPaginate: {
                sFirst: "<<",
                sLast: ">>",
                sNext: ">",
                sPrevious: "<",
            },
            oAria: {
                sSortAscending:
                    ": aktifkan untuk mengurutkan kolom secara naik",
                sSortDescending:
                    ": aktifkan untuk mengurutkan kolom secara menurun",
            },
            select: {
                rows: {
                    _: "Terpilih %d baris",
                    0: "Klik sebuah baris untuk memilih",
                    1: "Terpilih 1 baris",
                },
            },
            buttons: {
                print: "Cetak",
                copy: "Salin",
                copyTitle: "Salin ke papan klip",
                copySuccess: {
                    _: "%d baris disalin",
                    1: "1 baris disalin",
                },
            },
        },
        pagingType: "full",
        pageLength: 10,
        dom: "<'row'<'col-sm-12'tr>>" + "<'row'<'col-12 mb-3'i><'col-12'p>>",
    });
};

function formatFileSize(sizeInKB) {
    if (sizeInKB < 1024) {
        return sizeInKB.toFixed(2) + " KB";
    } else if (sizeInKB < 1024 * 1024) {
        return (sizeInKB / 1024).toFixed(2) + " MB";
    } else {
        return (sizeInKB / (1024 * 1024)).toFixed(2) + " GB";
    }
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(amount);
}

const initDateRange = (typeReport, callbackFunction) => {
    $('#daterange').daterangepicker({
        opens: 'right',
        maxDate: moment(),
        ranges: {
            'Hari Ini': [moment(), moment()],
            'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
            '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
            'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
            'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                'month').endOf('month')],
            'Tahun Ini': [moment().startOf('year'), moment().endOf('year')],
            'Tahun Kemarin': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1,
                'year').endOf('year')],
        },
        locale: {
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Pilih',
            cancelLabel: 'Batal',
            fromLabel: 'Dari',
            toLabel: 'Ke',
            customRangeLabel: 'Custom',
            weekLabel: 'W',
            daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ],
            firstDay: 1
        }
    });

    // if (typeReport == 'Harian') {
    //     $('#daterange').data('daterangepicker').setStartDate(moment('{{ $date[0] }}', 'YYYY-MM-DD')
    //         .format('DD/MM/YYYY'));
    //     $('#daterange').data('daterangepicker').setEndDate(moment('{{ $date[1] }}', 'YYYY-MM-DD')
    //         .format('DD/MM/YYYY'));
    // } else {
    $('#daterange').val(null);
    // }

    $('#daterange').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
            'YYYY-MM-DD'));
        $("#month").val(null);
        callbackFunction('harian')
    });
    $('#daterange').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val(null);
    });
}

// Function to initialize DataTable based on screen size
const initializeDataTable = (tableId, config = {}) => {
    configScroll = { ...config, scrollX: true };
    // Get the screen width
    var screenWidth = $(window).width();

    // Check if the screen width is below 992 pixels
    if (screenWidth < 992) {
        // Initialize DataTable without scrollX
        $(`#${tableId}`).DataTable(configScroll);
    } else {
        // Initialize DataTable with scrollX
        $(`#${tableId}`).DataTable(config);
    }
}

const calculateAverage = (data) => {
    const sum = data.reduce((acc, value) => acc + value, 0);
    const average = sum / data.length;

    // Format the average value with two decimal places
    return parseFloat(average.toFixed(0));
}

const debounce = (func, delay) => {
    let timeoutId;
    return function () {
        const context = this;
        const args = arguments;
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func.apply(context, args);
        }, delay);
    };
}

const initializationSelect2Merk = (selectId, url) => {
    $(`#${selectId}`).select2({
        theme: "bootstrap-5",
        placeholder: 'Masukkan Merk Barang',
        width: '100%',
        allowClear: true,
        minimumInputLength: 1, // Minimum characters required to start searching
        language: {
            inputTooShort: function (args) {
                var remainingChars = args.minimum - args.input.length;
                return "Masukkan kata kunci setidaknya " + remainingChars +
                    " karakter";
            },
            searching: function () {
                return "Sedang mengambil data...";
            },
            noResults: function () {
                return "Merk tidak ditemukan";
            },
            errorLoading: function () {
                return "Terjadi kesalahan saat memuat data";
            },
        },
        templateSelection: function (data, container) {
            if (data.id === '') {
                return data.text;
            }
            var match = data.text.match(/^(.*?) \(/);
            var resultName = match[1];

            return $('<span class="custom-selection">' + resultName + '</span>');
        },
        ajax: {
            url: url, // URL to fetch data from
            dataType: 'json', // Data type expected from the server
            processResults: function (response) {
                var merks = response.data.merks;
                var options = [];

                merks.forEach(function (merk) {
                    options.push({
                        id: merk.id, // Use the merk
                        text: merk.merk + ' (' + merk.keterangan +
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
}