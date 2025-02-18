
$(document).ready(function () {
    // Check if student_token already exists
    let existingToken = localStorage.getItem("student_token");

    if (existingToken) {

        sessionStorage.setItem("successMessage", "You are already logged in.");
        window.location.href = "/student/dashboard";

    } else {

        $("#studentLoginForm").submit(function (e) {
            e.preventDefault();

            let id = $("#student_id").val();
            let password = $("#password").val();

            $.ajax({
                url: "http://127.0.0.1:8000/api/student/login",
                type: "POST",
                data: JSON.stringify({
                    student_id: id,
                    password: password
                }),
                contentType: "application/json",
                success: function (response) {
                    if (response.status) {
                        localStorage.setItem("student_token", response
                            .token); // ✅ Save token
                        sessionStorage.setItem("successMessage", response.message);
                        window.location.href = "/student/dashboard";
                    }
                },
                error: function (xhr) {
                    let errorMessage = xhr.responseJSON ? xhr.responseJSON.message :
                        "Login failed";
                    toastr.error(errorMessage);
                }
            });
        });
    }
})
