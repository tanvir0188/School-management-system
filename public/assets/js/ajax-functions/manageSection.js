var SectionManager = (function () {
    let adminToken = localStorage.getItem("admin_token");

    if (!adminToken) {
        toastr.error("Unauthorized access. Admin token is missing.");
        return;
    }

    // Extract sectionId from the URL
    let url = window.location.href;
    let urlParts = url.split('/');
    let sectionId = urlParts[urlParts.length - 1];
    let sectionName = urlParts[urlParts.length - 2];
    let className = urlParts[urlParts.length - 3];

    $(".breadcrumb").text(`Class: ${className}`);
    $(".page-title").text(`Section: ${sectionName}`);
    document.title = `Section: ${sectionName}`;

    function fetchSectionInfo() {
        $.ajax({
            url: `/api/admin/get-section-info/${sectionId}`,
            type: "GET",
            headers: { Authorization: `Bearer ${adminToken}` },
            success: function (response) {
                if (response.status) {
                    renderTeacher(response.teacher);
                    renderStudents(response.students);
                    $("#studentCount").text(response.student_count);
                } else {
                    toastr.error(response.message || "Failed to load data.");
                }
            },
            error: function (xhr) {
                toastr.error("An error occurred while fetching data.");
                console.error(xhr);
            }
        });
    }

    function renderTeacher(teacher) {
        $("#teacherInfo").html(`
            <div class="col">
                <h6>Teacher</h6>
                <p class="text-sm mb-0">
                    <div class="d-flex justify-content-between">
                        <div>
                            <i class="fa fa-user text-info" aria-hidden="true"></i>
                            <span class="font-weight-bold ms-1">${teacher.name}</span>
                        </div>
                        <div>
                            <button class="text-warning change-teacher" data-id="${teacher.id}" style="border: none; background: none; padding: 0;">
                                <span class="badge bg-warning">Change</span>
                            </button>
                        </div>
                    </div>
                </p>
            </div>
        `);
    }

    function renderStudents(students) {
        let studentHtml = "";
        students.forEach(student => {
            studentHtml += `
                <tr>
                    <td>
                        <img src="${student.photo ? `/students/${student.photo}` : '/media/nullPic.webp'}" 
                             alt="Student Photo" width="50">
                    </td>
                    <td>${student.full_name ? student.full_name : student.name}</td>
                    <td>${student.email}</td>
                    <td>${student.phone_number ? student.phone_number : 'N/A'}</td>
                    <td>${student.address ? student.address : 'N/A'}</td>
                    <td>
                        <button class="text-warning remove-student" data-id="${student.id}" style="border: none; background: none; padding: 0;">
                            <span class="badge bg-warning">Remove</span>
                        </button>
                    </td>
                </tr>
            `;
        });
        $("#studentData").html(studentHtml);
    }

    function openChangeTeacherModal() {
        $("#changeTeacher").modal("show");

        // Fetch available teachers
        $.ajax({
            url: "/api/admin/teacher-without-pagination/index",
            type: "GET",
            headers: { Authorization: `Bearer ${adminToken}` },
            success: function (response) {
                if (response.status && response.teachers && response.teachers.teachers) {
                    let teacherOptions = `<option selected disabled>Select teacher</option>`;
                    response.teachers.teachers.forEach(teacher => {
                        teacherOptions += `<option value="${teacher.id}">${teacher.name}</option>`;
                    });
                    $("#selectTeacher").html(teacherOptions);
                } else {
                    toastr.error("No teachers found.");
                }
            },
            error: function () {
                toastr.error("Failed to load teachers.");
            }
        });
    }


    function updateTeacher() {
        let teacherId = $("#selectTeacher").val();
        if (!teacherId) {
            toastr.error("Please select a teacher.");
            return;
        }

        $.ajax({
            url: `/api/admin/section/change-teacher/${sectionId}`,
            type: "PATCH",
            headers: { Authorization: `Bearer ${adminToken}` },
            data: { teacher_id: teacherId },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    $("#changeTeacher").modal("hide");
                    fetchSectionInfo(); // Refresh data
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "Failed to update teacher.";
                toastr.error(errorMessage);
            }
        });
    }

    // Event Listeners
    $(document).on("click", ".change-teacher", openChangeTeacherModal);
    $("#updateTeacherButton").on("click", updateTeacher);

    return {
        fetchSectionInfo: fetchSectionInfo,
        renderTeacher: renderTeacher,
        renderStudents: renderStudents,
        openChangeTeacherModal: openChangeTeacherModal,
        updateTeacher: updateTeacher
    };
})();

// Initialize Data Fetching
SectionManager.fetchSectionInfo();
