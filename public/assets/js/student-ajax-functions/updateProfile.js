$(document).ready(function () {
    let studentToken = localStorage.getItem("student_token");
    let studentInfo = localStorage.getItem("student"); // Assuming you store it somewhere
    let student = JSON.parse(studentInfo);
    let studentId = student.id;
    console.log(studentId);

    if (!studentToken) {
        toastr.error("Unauthorized access. Student token is missing.");
        return;
    }

    // Fetch student data and fill the form
    $.ajax({
        url: `http://127.0.0.1:8000/api/studentProfile/${studentId}`, // Update this to your actual API endpoint
        type: "GET",
        
        success: function (response) {
            if (response.status) {
                $("#full_name").val(response.studentProfile.full_name);
                $("#student_id").val(response.studentProfile.student_id);
                $("#phone_number").val(response.studentProfile.phone_number);
                $("#age").val(response.studentProfile.age);
                $("#father_name").val(response.studentProfile.father_name);
                $("#mother_name").val(response.studentProfile.mother_name);
                $("#address").val(response.studentProfile.address);
            } else {
                toastr.error(response.message);
            }
        },
        error: function () {
            toastr.error("Failed to fetch student profile.");
        }
    });
});

$("#studentProfileForm").submit(function (e) {
    e.preventDefault();
    let studentToken = localStorage.getItem("student_token");
    let studentId = $("#student_id").val();

    let formData = new FormData(this);
    formData.append("_method", "PUT"); // Laravel requires PUT method

    $.ajax({
        url: `http://127.0.0.1:8000/api/student/studentProfile/${studentId}`,
        type: "POST", // Laravel requires POST + `_method: PUT`
        headers: { "Authorization": `Bearer ${studentToken}` },
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.status) {
                sessionStorage.setItem("successMessage", response.message);
                let studentLoginInfo = localStorage.getItem("student_login_info");
                if (studentLoginInfo) {
                    studentLoginInfo = JSON.parse(studentLoginInfo);
                    studentLoginInfo.student_profile = response.studentProfile; // Update profile
                    localStorage.setItem("student_login_info", JSON.stringify(studentLoginInfo));
                }

                window.location.href = "/student/profile"; // Redirect to profile page
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

