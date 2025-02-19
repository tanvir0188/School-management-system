$(document).ready(function () {
    // Check if student_token already exists
    let existingToken = localStorage.getItem("teacher_token");

    if (existingToken) {
        sessionStorage.setItem("successMessage", "You are already logged in.");
        window.location.href = "/teacher/dashboard";
    } else {
        $("#teacherLoginForm").submit(function (e) {
            e.preventDefault();

            let email = $("#email").val();
            let password = $("#password").val();

            // Perform login
            $.ajax({
                url: "http://127.0.0.1:8000/api/teacher/login",
                type: "POST",
                data: JSON.stringify({
                    email: email,
                    password: password
                }),
                contentType: "application/json",
                success: function (loginResponse) {
                    if (loginResponse.status) {
                        // Store login token and student data in localStorage
                        localStorage.setItem("teacher_token", loginResponse.token);
                        localStorage.setItem("teacher", JSON.stringify(loginResponse.teacher));

                        // Fetch additional login info using the student ID
                        $.ajax({
                            url: `http://127.0.0.1:8000/api/teacher/getLoginInfos/${loginResponse.teacher.id}`,
                            type: "GET",
                            headers: {
                                "Authorization": `Bearer ${loginResponse.token}` // Include the token in the header
                            },
                            success: function (infoResponse) {
                                if (infoResponse.status) {
                                    // Store additional login info in localStorage
                                    localStorage.setItem("teacher_login_info", JSON.stringify(infoResponse.data));

                                    // Redirect to dashboard
                                    sessionStorage.setItem("successMessage", loginResponse.message);
                                    window.location.href = "/teacher/dashboard";
                                } else {
                                    toastr.error("Failed to fetch login information.");
                                }
                            },
                            error: function (xhr) {
                                let errorMessage = xhr.responseJSON ? xhr.responseJSON.message :
                                    "Failed to fetch login information.";
                                toastr.error(errorMessage);
                            }
                        });
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
});