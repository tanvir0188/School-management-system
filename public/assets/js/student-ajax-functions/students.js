$(document).ready(function () {
    let studentToken = localStorage.getItem("student_token");

    if (!studentToken) {
        toastr.error("Unauthorized access. Student token is missing.");
        return;
    }
    let student = localStorage.getItem("student");
    student = JSON.parse(student);
    let currentSectionId = student.sec_id;
    if (!currentSectionId) {
        toastr.error("Section ID is missing.");
        return;
    }

    $.ajax({
        url: `http://127.0.0.1:8000/api/student/section-students/${currentSectionId}`, // Adjust API endpoint if needed
        type: "GET",
        headers: {
            "Authorization": "Bearer " + studentToken
        },
        dataType: "json",
        success: function (response) {
            if (response.status) {
                let students = response.students;
                let tableBody = "";
                $('#studentCount').text(students.length)

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
        error: function () {
            toastr.error("Failed to fetch student data.");
        }
    });
});