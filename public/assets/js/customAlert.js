const errorAlert = (title, msg) => {
    Swal.fire({
        title: title,
        icon: "error",
        text: msg,
        showCancelButton: false,
        confirmButtonText: "Okay",
        customClass: {
            confirmButton: "btn btn-danger",
        },
    });
};

const successAlert = (title, msg) => {
    Swal.fire({
        title: title,
        text: msg,
        icon: "success",
        showCancelButton: false,
        confirmButtonText: "Okay",
        customClass: {
            confirmButton: "btn btn-success",
        },
    });
};

const warningAlert = (title, msg) => {
    Swal.fire({
        title: title,
        icon: "warning",
        text: msg,
        showCancelButton: false,
        confirmButtonText: "Okay",
        customClass: {
            confirmButton: "btn btn-warning",
        },
    });
};

const infoAlert = (title, msg) => {
    Swal.fire({
        title: title,
        icon: "info",
        text: msg,
        showCancelButton: false,
        confirmButtonText: "Okay",
        customClass: {
            confirmButton: "btn btn-info",
        },
    });
};
