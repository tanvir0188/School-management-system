$(document).ready(function () {
    // Retrieve teacher login info from localStorage
    let teacherLoginInfo = localStorage.getItem('teacher_login_info');
    if (!teacherLoginInfo) {
        console.error("Teacher login info not found in localStorage");
        return;
    }

    // Parse the data
    let teacherData = JSON.parse(teacherLoginInfo);

    // Check if the teacher has designated sections
    if (teacherData.has_designated_sections) {
        // Loop through each class-section pair and create cards
        let cardsHtml = '';
        for (const [classId, classInfo] of Object.entries(teacherData.classes)) {
            cardsHtml += `
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <a href="/teacher/section-students/${classInfo.class_name}/${classInfo.section}/${classInfo.sectionId}" data-sectionId="${classInfo.sectionId}">
                        <div class="card">
                            <span class="mask bg-dark opacity-10 border-radius-lg"></span>
                            <div class="card-body p-3 position-relative">
                                <div class="row">
                                    <div class="col-8 text-start">
                                        <h5 class="text-white font-weight-bolder mb-0 mt-3 section-name id">
                                            Section: ${classInfo.section}
                                        </h5>
                                        <span class="text-white text-sm class-name">
                                            Class: ${classInfo.class_name}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            `;
        }

        // Append the cards to the container
        $(".row .container .row").html(cardsHtml);
    } else {
        // If no sections are assigned, display a message
        $(".row .container .row").html(`
            <div class="col-12 text-center">
                <h3 class="text-black">You haven't been designated to any section. Please wait.</h3>
            </div>
        `);
    }
});