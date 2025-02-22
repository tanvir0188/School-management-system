
$(document).ready(function () {
    fetchClassesAndSections();
});

function fetchClassesAndSections() {
    let adminToken = localStorage.getItem("admin_token");
    if (!adminToken) {
        toastr.error("Unauthorized access. Admin token is missing.");
        return;
    }
    $.ajax({
        url: '/api/admin/get-section-and-class', // Replace with your actual API URL
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + adminToken,
        },
        dataType: 'json',
        success: function (response) {
            if (response.status) {
                generateCards(response.data.classes);
            } else {
                console.error("Error fetching data");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error:", error);
        }
    });
}

function generateCards(classes) {
    let container = $('.container .row'); // Target the correct container inside your row
    container.empty(); // Clear existing content

    $.each(classes, function (classId, classData) {
        $.each(classData.sections, function (index, section) {
            let teacherName = section.teacher_name ? `Teacher: ${section.teacher_name}` : "No teacher assigned yet";
            let studentCount = section.student_count ?? 0; // Get student count from API response

            let cardHtml = `
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <a href="/admin/section/${classData.class_name}/${section.section_name}/${section.section_id}" class="card-link">
                        <div class="card">
                            <span class="mask bg-secondary hover-effect opacity-10 border-radius-lg"></span>
                            <div class="card-body px-3 py-2 position-relative">
                                <div class="row">
                                    <div class="col-8 text-start">
                                        <h5 class="text-white font-weight-bolder mb-0 mt-3">Class: <span>${classData.class_name}</span></h5>
                                        <p class="section-name text-white">Section: ${section.section_name}</p>
                                        <p class="student-count text-white">Students: ${studentCount}</p>
                                        <p class="teacher-name text-white text-sm">${teacherName}</p>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            `;

            container.append(cardHtml);
        });
    });
}

