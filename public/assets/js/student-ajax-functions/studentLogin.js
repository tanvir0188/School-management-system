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

            // Perform login
            $.ajax({
                url: "http://127.0.0.1:8000/api/student/login",
                type: "POST",
                data: JSON.stringify({
                    student_id: id,
                    password: password
                }),
                contentType: "application/json",
                success: function (loginResponse) {
                    if (loginResponse.status) {
                        // Store login token and student data in localStorage
                        localStorage.setItem("student_token", loginResponse.token);
                        localStorage.setItem("student", JSON.stringify(loginResponse.student));

                        // Fetch additional login info using the student ID
                        $.ajax({
                            url: `http://127.0.0.1:8000/api/student/getLoginInfos/${loginResponse.student.id}`,
                            type: "GET",
                            headers: {
                                "Authorization": `Bearer ${loginResponse.token}` // Include the token in the header
                            },
                            success: function (infoResponse) {
                                if (infoResponse.status) {
                                    // Store additional login info in localStorage
                                    localStorage.setItem("student_login_info", JSON.stringify(infoResponse.data));

                                    // Redirect to dashboard
                                    sessionStorage.setItem("successMessage", loginResponse.message);
                                    window.location.href = "/student/dashboard";
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