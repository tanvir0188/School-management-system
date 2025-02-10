<script>
    document.addEventListener("DOMContentLoaded", function () {
        let token = localStorage.getItem("admin_token"); // Use localStorage

        if (!token) {
            window.location.href = "http://127.0.0.1:8000/admin-sign-in"; 
            return; 
        }
    });
</script>