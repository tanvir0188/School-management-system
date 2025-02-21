
$(document).ready(function () {
    let adminToken = localStorage.getItem("admin_token");
    let currentSearchParams = {}; // Store search parameters globally
    if (!adminToken) {
        toastr.error("Unauthorized access. Admin token is missing.");
        return;
    }



    fetchExams();
    $("#searchButton").on("click", function (e) {
        e.preventDefault(); // Prevents form reload

        let searchValue = $("#searchExams").val().trim();

        currentSearchParams = {
            search: searchValue
        }; // Store search params globally

        fetchExams(1, currentSearchParams);
    });




    function fetchExams(page = 1, searchParams = {}) {

        $.ajax({
            url: `http://127.0.0.1:8000/api/public/exams?page=${page}`,
            type: "GET",
            data: searchParams,
            dataType: "json",

            success: function (response) {
                if (response.status) {
                    let exams = response.exams.data; // Access nested data
                    let examCount = response.examCount;
                    let searchedExam = $('#examCount');
                    searchedExam.text(examCount);
                    console.log('count:' + examCount);

                    let examTable = $("#examData");
                    examTable.empty();

                    $.each(exams, function (index, exam) {
                        console.log(exam);
                        let row = `
                        <tr>
                            <td>${exam.exam_type_name}</td>
                            <td>${exam.class_name}</td>
                            <td>${exam.subject}</td>
                            <td><span class="badge bg-primary">${exam.full_marks}</span></td>
                            <td><span class="badge bg-secondary">${exam.exam_date}</span></td>
                            <td>
                                <a href="#" class="text-danger delete-exam" data-id="${exam.exam_id}">
                                    <span class="badge bg-danger"><i class="fa-solid fa-trash"></i></span>
                                </a>
                                <button class="text-warning edit-exam" data-id="${exam.exam_id}" style="border: none; background: none; padding: 0;">
                                    <span class="badge bg-warning"><i class="fa-solid fa-edit"></i></span>
                                </button>
                            </td>
                        </tr>
                    `;
                        examTable.append(row);
                    });

                    $("#paginationLinks").html(generatePagination(response.exams));
                }
            },
            error: function (xhr) {
                let errorMessage = xhr.responseJSON?.message || "Failed to load data";
                $("#examCount").text(0);
                $("#examData").html(
                    `<tr><td colspan="6" class="text-center text-danger">${errorMessage}</td></tr>`
                );
                $("#paginationLinks").html("");
            }
        });
    }



    function deleteExam(examId) {
        $.ajax({
            url: `http://127.0.0.1:8000/api/admin/exam/${examId}`,
            type: "DELETE",
            dataType: "json",
            headers: {
                "Authorization": "Bearer " + adminToken
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message); // Show success message
                    fetchExams(); // Refresh the table data
                } else {
                    toastr.error(response.message); // Show error message
                }
            },
            error: function (xhr) {
                let errorMessage = xhr.responseJSON?.message || "Failed to delete exam";
                toastr.error(errorMessage); // Show error message
            }
        });
    }
    $(document).on("click", ".delete-exam", function (e) {
        e.preventDefault(); // Prevent default link behavior
        let examId = $(this).data("id"); // Get the exam ID
        if (confirm("Are you sure you want to delete this exam?")) {
            deleteExam(examId); // Call the delete function
        }
    });

    function generatePagination(data) {
        let paginationHtml = '<nav><ul class="pagination justify-content-center">';

        if (data.prev_page_url) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${data.current_page - 1}"><</a>
                </li>`;
        }

        for (let i = 1; i <= data.last_page; i++) {
            paginationHtml += `
                <li class="page-item ${i === data.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
        }

        if (data.next_page_url) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${data.current_page + 1}">></a>
                </li>`;
        }

        paginationHtml += '</ul></nav>';
        return paginationHtml;
    }

    $(document).on("click", "#paginationLinks .page-link", function (e) {
        e.preventDefault();
        let page = $(this).data("page");
        fetchExams(page, currentSearchParams); // Use stored search params when paginating
    });

    $(document).on("click", ".edit-exam", function (e) {
        e.preventDefault(); // Prevent default button behavior
        const examId = $(this).data("id");
        

        // Fetch the exam details
        $.ajax({
            url: `http://127.0.0.1:8000/api/exam/${examId}`,
            type: "GET",
            headers: {
                "Authorization": "Bearer " + adminToken,
            },
            success: function (response) {
                if (response.status) {
                    // Fetch classes and exam types first
                    fetchClassesAndExamTypes(response.exam);
                } else {
                    toastr.error(response.message || "Failed to fetch exam details.");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                let errorMessage = "Failed to fetch exam details. Please try again later.";

                // Check if the response contains a JSON message
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    errorMessage = jqXHR.responseJSON.message;
                }

                toastr.error(errorMessage);
            },
        });
    });

    // Function to fetch classes and exam types
    function fetchClassesAndExamTypes(exam) {

        // Fetch Classes
        $.ajax({
            url: "http://127.0.0.1:8000/api/class/index",
            type: "GET",
            success: function (response) {
                console.log("API Response:", response); // Log the full response

                let classList = response.classes.classes; // Access nested array

                if (response.status && Array.isArray(classList)) {
                    let classDropdown = $("#updateClassName");
                    classDropdown.empty().append(
                        '<option selected disabled>Select a class</option>'
                    );

                    classList.forEach((cls) => {
                        classDropdown.append(
                            `<option value="${cls.id}">${cls.name}</option>`
                        );
                    });

                    // Set the selected class after populating the dropdown
                    classDropdown.val(exam.class_id);
                } else {
                    console.error("Error: 'classes.classes' is not an array", response.classes);
                    toastr.error("Unexpected response format.");
                }
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
                toastr.error("Failed to load classes.");
            },
        });

        // Fetch Exam Types
        $.ajax({
            url: "http://127.0.0.1:8000/api/exam-type",
            type: "GET",
            success: function (response) {
                console.log("API Response:", response); // Debugging

                let examTypeList = response.examTypes.examTypes;

                if (response.status && Array.isArray(examTypeList)) {
                    let examTypeDropdown = $("#updateExamType");
                    examTypeDropdown.empty().append(
                        '<option selected disabled>Select exam type</option>'
                    );

                    examTypeList.forEach((examType) => {
                        examTypeDropdown.append(
                            `<option value="${examType.id}">${examType.name}</option>`
                        );
                    });

                    // Set the selected exam type after populating the dropdown
                    examTypeDropdown.val(exam.exam_type_id);
                } else {
                    toastr.error("Unexpected response format.");
                }

                // Now that both dropdowns are populated, open the modal
                $("#updateSubject").val(exam.subject);
                $("#updateFullMark").val(exam.full_marks);
                $("#updateExamDate").val(exam.exam_date.split("T")[0]); // Format date
                $("#updateExamId").val(exam.id); // Set the exam ID

                // Open the modal
                $("#updateExamModal").modal("show");
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
                toastr.error("Failed to load exam types.");
            },
        });
    }
    $(document).on("click", "#updateExamButton", function (e) {
        e.preventDefault(); 
        const examId = $("#updateExamId").val();

        // Get form data
        const formData = {
            exam_type_id: $("#updateExamType").val(),
            class_id: $("#updateClassName").val(),
            subject: $("#updateSubject").val(),
            full_marks: $("#updateFullMark").val(),
            exam_date: $("#updateExamDate").val(),
        };

        // Send the AJAX request to update the exam
        $.ajax({
            url: `http://127.0.0.1:8000/api/admin/exam/${examId}`,
            type: "PUT",
            headers: {
                "Authorization": "Bearer " + adminToken,
                "Content-Type": "application/json",
            },
            data: JSON.stringify(formData),
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message || "Exam updated successfully.");
                    $("#updateExamModal").modal("hide"); // Close the modal
                    fetchExams(); // Refresh the exams list

                } else {
                    toastr.error(response.message || "Failed to update exam.");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                let errorMessage = "Failed to update exam. Please try again later.";

                // Check if the response contains a JSON message
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    errorMessage = jqXHR.responseJSON.message;
                }

                toastr.error(errorMessage);
            },
        });
    });
});
