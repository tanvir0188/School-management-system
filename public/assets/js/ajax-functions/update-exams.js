$(document).ready(function () {
    $(document).on("click", ".edit-exam", function (e) {
        e.preventDefault(); // Prevent default button behavior

        const examId = $(this).data("id"); // Get the exam ID from the data-id attribute
        const adminToken = localStorage.getItem("admin_token");

        if (!adminToken) {
            toastr.error("Unauthorized access. Admin token is missing.");
            return;
        }

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
        const adminToken = localStorage.getItem("admin_token");

        // Fetch Classes
        $.ajax({
            url: "http://127.0.0.1:8000/api/class/index",
            type: "GET",
            headers: {
                "Authorization": "Bearer " + adminToken,
            },
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
            headers: {
                "Authorization": "Bearer " + adminToken,
            },
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
        e.preventDefault(); // Prevent default button behavior

        const adminToken = localStorage.getItem("admin_token");
        const examId = $("#updateExamId").val(); // Get the exam ID from the hidden field

        if (!adminToken) {
            toastr.error("Unauthorized access. Admin token is missing.");
            return;
        }

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