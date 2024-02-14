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
