const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 1500,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

function showSuccessToast(message) {
    Toast.fire({
        icon: "success",
        title: message,
        background: "#28a745", // Green background for success
        color: "#fff"
    });
}

function showErrorToast(message) {
    Toast.fire({
        icon: "error",
        title: message,
        background: "#dc3545", // Red background for error
        color: "#fff"
    });
}
