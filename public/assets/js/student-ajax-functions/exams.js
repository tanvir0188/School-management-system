$(document).ready(function () {
    let studentInfo = localStorage.getItem("student_login_info");
    let student = localStorage.getItem("student");
    let studentToken = localStorage.getItem("student_token");

    if (studentInfo && student) {
        studentInfo = JSON.parse(studentInfo);
        student = JSON.parse(student);

        let studentId = student.id;

        // API call to fetch exam results for the student
        $.ajax({
            url: `http://127.0.0.1:8000/api/student/exams/${studentId}`,
            type: "GET",
            headers: {
                "Authorization": `Bearer ${studentToken}`
            },
            dataType: "json",
            success: function (response) {
                if (response.status) {
                    console.log( response);
                    generateExamTables(response.results);
                } else {
                    $("#Data").html(`<tr><td colspan="5" class="text-center">No results found</td></tr>`);
                }
            },
            error: function () {
                $("#Data").html(`<tr><td colspan="5" class="text-center">Failed to fetch data</td></tr>`);
            },
        });
    }
});

// Function to dynamically generate tables for each exam type
function generateExamTables(results) {
    let container = $("#resultContainer"); // Reference to the container
    container.empty(); // Clear any existing content

    Object.keys(results).forEach((examType) => {
        let tableHtml = `
            <div class="row my-4 mb-4">
                <div class="col-lg-8 mb-md-0 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-lg-6 col-7">
                                    <h6 class="exam-title">${examType}</h6>
                                    <p class="text-sm mb-0">
                                        <i class="fa fa-check text-info" aria-hidden="true"></i>
                                        <span class="font-weight-bold ms-1">${results[examType].length}</span> exams
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <th class="text-uppercase text-secondary text-xxs text-center font-weight-bolder opacity-7">Subject</th>
                                        <th class="text-uppercase text-secondary text-xxs text-center font-weight-bolder opacity-7">Marks</th>
                                        <th class="text-uppercase text-secondary text-xxs text-center font-weight-bolder opacity-7">Full Marks</th>
                                        <th class="text-uppercase text-secondary text-xxs text-center font-weight-bolder opacity-7">Highest Mark</th>
                                        <th class="text-uppercase text-secondary text-xxs text-center font-weight-bolder opacity-7">Exam Date</th>
                                    </thead>
                                    <tbody>`;

        results[examType].forEach((exam) => {
            tableHtml += `
                <tr>
                    <td class="text-center">${exam.subject}</td>
                    <td class="text-center">${exam.marks}</td>
                    <td class="text-center">${exam.full_marks}</td>
                    <td class="text-center">${exam.highest}</td>
                    <td class="text-center">${exam.exam_date}</td>
                </tr>`;
        });

        tableHtml += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

        container.append(tableHtml); // Append each table to the container
    });
}