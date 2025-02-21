$(document).ready(function () {
    let teacherToken = localStorage.getItem("teacher_token");

    if (!teacherToken) {
        toastr.error("Unauthorized access. Teacher token is missing.");
        return;
    }

    // Extract sectionId from the URL
    let url = window.location.href;
    let urlParts = url.split('/');
    let sectionId = urlParts[urlParts.length - 1];
    let sectionName = urlParts[urlParts.length - 2];
    let className = urlParts[urlParts.length - 3];  

    if (!sectionId) {
        toastr.error("Section ID is missing.");
        return;
    }
    $(".breadcrumb").text(`Class: ${className}`);
    $(".page-title").text(`Section: ${sectionName}`);
    document.title = `Section: ${sectionName}`;

    $.ajax({
        url: `http://127.0.0.1:8000/api/teacher/section-students/${sectionId}`,
        type: "GET",
        headers: {
            "Authorization": "Bearer " + teacherToken
        },
        dataType: "json",
        success: function (response) {
            
            if (response.status) {
                let students = response.students;
                let tableBody = "";
                $('#studentCount').text(students.length);

                // Update breadcrumb and page title
                

                students.forEach(student => {
                    let studentName = student.full_name ? student.full_name : student.name;

                    tableBody += `
                        <tr>
                            <td>
                                <img src="${student.photo ? `/students/${student.photo}` : '/media/nullPic.webp'}" 
                                    alt="Student Photo" 
                                    width="50">
                            </td>
                            <td>${studentName}</td>
                            <td>${student.email}</td>
                            <td>${student.phone_number ? student.phone_number : 'N/A'}</td>
                            <td>${student.address ? student.address : 'N/A'}</td>
                        </tr>
                    `;
                });

                $("#studentData").html(tableBody);
            } else {
                $("#studentData").html(
                    `<tr><td colspan="5" class="text-center text-danger">${response.message}</td></tr>`
                );
            }
        },
        error: function (xhr) {
            
            if (xhr.status === 404 && xhr.responseJSON) {
                $("#studentTable").hide(); // Hide table when no students are found
                $("#student-card-body").append(`<h5 class="text-center text-danger">${xhr.responseJSON.message}</h5>`);
            } else {
                toastr.error("Failed to fetch student data.");
            }
        }
    });

});