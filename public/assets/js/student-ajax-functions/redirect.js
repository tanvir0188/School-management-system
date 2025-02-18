
document.addEventListener("DOMContentLoaded", function() {
    let token = localStorage.getItem("student_token"); // Use localStorage

    if (!token) {
        window.location.href = "http://127.0.0.1:8000/student-login";
        return;
    }
});
