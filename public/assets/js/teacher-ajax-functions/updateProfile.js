$(document).ready(function () {
    let teacherToken = localStorage.getItem("teacher_token");
    let teacherInfo = localStorage.getItem("teacher"); // Assuming you store it somewhere
    let teacher = JSON.parse(teacherInfo);
    let teacherId = teacher.id;
    console.log(teacherId);

    if (!teacherToken) {
        toastr.error("Unauthorized access. Teacher token is missing.");
        return;
    }

    // Fetch teacher data and fill the form
    $.ajax({
        url: `http://127.0.0.1:8000/api/teacherProfile/${teacherId}`, // Update this to your actual API endpoint
        type: "GET",
        
        success: function (response) {
            if (response.status) {
                $("#full_name").val(response.teacherProfile.full_name);
                $("#teacher_id").val(response.teacherProfile.teacher_id);
                $("#phone_number").val(response.teacherProfile.phone_number);
                $("#age").val(response.teacherProfile.age);
                $("#father_name").val(response.teacherProfile.father_name);
                $("#mother_name").val(response.teacherProfile.mother_name);
                $("#address").val(response.teacherProfile.address);
                $("#description").val(response.teacherProfile.description);
            } else {
                toastr.error(response.message);
            }
        },
        error: function () {
            toastr.error("Failed to fetch teacher profile.");
        }
    });
});

$("#teacherProfileForm").submit(function (e) {
    e.preventDefault();
    let teacherToken = localStorage.getItem("teacher_token");
    let teacherId = $("#teacher_id").val();

    let formData = new FormData(this);
    formData.append("_method", "PUT"); // Laravel requires PUT method

    $.ajax({
        url: `http://127.0.0.1:8000/api/teacher/teacherProfile/${teacherId}`,
        type: "POST", // Laravel requires POST + `_method: PUT`
        headers: { "Authorization": `Bearer ${teacherToken}` },
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.status) {
                sessionStorage.setItem("successMessage", response.message);
                let teacherLoginInfo = localStorage.getItem("teacher_login_info");
                if (teacherLoginInfo) {
                    teacherLoginInfo = JSON.parse(teacherLoginInfo);
                    teacherLoginInfo.teacher_profile = response.teacherProfile; // Update profile
                    localStorage.setItem("teacher_login_info", JSON.stringify(teacherLoginInfo));
                }

                window.location.href = "/teacher/profile"; // Redirect to profile page
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr) {
            let errors = xhr.responseJSON?.errors || ["Update failed"];
            toastr.error(errors.join("<br>"));
        }
    });
});

