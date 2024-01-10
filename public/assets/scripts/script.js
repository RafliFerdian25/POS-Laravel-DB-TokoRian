// Data table
$(document).ready(function () {
    $("#barangkadaluarsa").DataTable();
});
$(document).ready(function () {
    $("#barang").DataTable();
});
$(document).ready(function () {
    $("#barang_terjual").DataTable();
});
$(document).ready(function () {
    $("#pendapatan_tertinggi").DataTable({
        pageLength: 3,
        paging: false,
        info: false,
    });
});

/****************  INDEX   ***************** */
function icon_perubahan(id) {
    if (parseInt($(id).text()) < 0) {
        $(id).before(`
        <i class="col-1 fas fa-arrow-circle-down color__red1 align-self-center"></i>`);
    } else {
        $(id).before(`
        <i class="col-1 fas fa-arrow-circle-up color__green1 align-self-center"></i>`);
    }
}
$(document).ready(function () {
    icon_perubahan("#total_pendapatan");
    icon_perubahan("#total_keuntungan");
    icon_perubahan("#total_order");
    icon_perubahan("#total_barang");
});

/****************  Kasir   ***************** */

function cetak() {
    let text = "Press a button!\nEither OK or Cancel.";
    if (confirm(text) == true) {
        text = "You pressed OK!";
    } else {
        text = "You canceled!";
    }
    document.getElementById("demo").innerHTML = text;
}
