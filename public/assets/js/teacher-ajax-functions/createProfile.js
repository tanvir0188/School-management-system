$(document).ready(function () {
    let teacherToken = localStorage.getItem("teacher_token");
    
    if (!teacherToken) {
        toastr.error("Unauthorized access. Teacher token is missing.");
        return;
    }
    let teacherInfo = localStorage.getItem("teacher"); // Assuming you store it somewhere
    let teacher = JSON.parse(teacherInfo);
    let teacherId = teacher.id;
    if (teacherId) {
        $("#teacher_id").val(teacherId);
    } else {
        toastr.error("Teacher ID is missing.");
        return;
    }
    $("#teacherProfileForm").submit(function (e) {
        e.preventDefault(); // Prevent default form submission

        let formData = new FormData(this);

        $.ajax({
            url: `http://127.0.0.1:8000/api/teacher/teacherProfile/store`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "Authorization": "Bearer " + teacherToken
            },
            success: function (response) {
                if (response.status) {
                    sessionStorage.setItem("successMessage", response.message);
                    let teacherLoginInfo = localStorage.getItem("teacher_login_info");
                    if (teacherLoginInfo) {
                        teacherLoginInfo = JSON.parse(teacherLoginInfo);
                        teacherLoginInfo.teacher_profile = response.teacherProfile; // Update profile
                        localStorage.setItem("teacher_login_info", JSON.stringify(teacherLoginInfo));
                    }

                    window.location.href = "/teacher/profile";
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