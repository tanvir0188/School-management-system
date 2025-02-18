$(document).ready(function () {
    let studentToken = localStorage.getItem("student_token");
    
    if (!studentToken) {
        toastr.error("Unauthorized access. student token is missing.");
        return;
    }
    let studentInfo = localStorage.getItem("student"); // Assuming you store it somewhere
    let student = JSON.parse(studentInfo);
    let studentId = student.id;
    if (studentId) {
        $("#student_id").val(studentId);
    } else {
        toastr.error("Student ID is missing.");
        return;
    }
    $("#studentProfileForm").submit(function (e) {
        e.preventDefault(); // Prevent default form submission

        let formData = new FormData(this);

        $.ajax({
            url: `http://127.0.0.1:8000/api/student/studentProfile/store`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "Authorization": "Bearer " + studentToken
            },
            success: function (response) {
                if (response.status) {
                    sessionStorage.setItem("successMessage", response.message);
                    let studentLoginInfo = localStorage.getItem("student_login_info");
                    if (studentLoginInfo) {
                        studentLoginInfo = JSON.parse(studentLoginInfo);
                        studentLoginInfo.student_profile = response.studentProfile; // Update profile
                        localStorage.setItem("student_login_info", JSON.stringify(studentLoginInfo));
                    }

                    window.location.href = "/student/profile";
                } else {
                    toastr.warning(response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    errors.forEach(function (error) {
                        toastr.error(error);
                    });
                } else {
                    toastr.error("Something went wrong. Please try again.");
                }
            }
        });
    });
    
});