$(document).ready(function() {
    // ✅ Retrieve the message stored in sessionStorage
    let successMessage = sessionStorage.getItem("successMessage");
    if (successMessage) {
        toastr.success(successMessage);
        sessionStorage.removeItem("successMessage");
    }
});