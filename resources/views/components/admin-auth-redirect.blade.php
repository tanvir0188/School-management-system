<script>
    document.addEventListener("DOMContentLoaded", function() {
        let token = localStorage.getItem("admin_token"); // Use localStorage

        if (!token) {
            window.location.href = "http://127.0.0.1:8000/admin-sign-in";
            return;
        }

        
    });
</script>
<script>
    $(document).ready(function() {
        // ✅ Retrieve the message stored in sessionStorage
        let successMessage = sessionStorage.getItem("successMessage");

        if (successMessage) {
            toastr.success(successMessage);
            sessionStorage.removeItem("successMessage");
        }
    });
</script>