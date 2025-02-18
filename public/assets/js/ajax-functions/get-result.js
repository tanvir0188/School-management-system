$(document).ready(function () {
    let adminToken = localStorage.getItem("admin_token");
    let currentSearchParams = {}; // Store search parameters globally
    if (!adminToken) {
        toastr.error("Unauthorized access. Admin token is missing.");
        return;
    }



    fetchResults();
    $("#searchButton").on("click", function (e) {
        e.preventDefault(); // Prevents form reload

        let searchValue = $("#searchResults").val().trim();

        currentSearchParams = {
            search: searchValue
        }; // Store search params globally

        fetchResults(1, currentSearchParams);
    });




    $("#searchButton").on("click", function (e) {
        e.preventDefault(); // Prevents form reload
        let searchValue = $("#searchResults").val().trim();
        currentSearchParams = { search: searchValue }; // Store search params globally
        fetchResults(1, currentSearchParams);
    });

    function fetchResults(page = 1, searchParams = {}) {
        $.ajax({
            url: `http://127.0.0.1:8000/api/admin/exam-results?page=${page}`,
            type: "GET",
            data: searchParams,
            dataType: "json",
            headers: { "Authorization": `Bearer ${adminToken}` },
            success: function (response) {
                if (response.status) {
                    let results = response.results.data; // Access nested data
                    let resultCount = response.resultCount;
                    $('#resultCount').text(resultCount);
                    console.log('count:' + resultCount);

                    let resultTable = $("#resultData");
                    resultTable.empty();

                    $.each(results, function (index, result) {
                        let row = `
                        <tr data-id="${result.id}">
                            <td>${result.exam_type_name}</td>
                            <td>${result.subject}</td>
                            <td>${result.class_name}</td>
                            <td>${result.section_name}</td>
                            <td>${result.student_name}</td>
                            <td>${result.student_id}</td>
                            <td><span class="badge bg-primary">${result.full_marks}</span></td>
                            <td><span class="badge bg-warning marks-cell">${result.marks}</span></td>
                            <td><span class="badge bg-secondary">${result.exam_date}</span></td>
                            <td>
                                <button type="button" data-toggle="modal" data-target="#exampleModal" class="text-warning update-exam badge bg-warning" data-exam-id="${result.exam_id}" data-student-id="${result.s_id}" data-marks="${result.marks}" data-full-mark="${result.full_marks}">
                                    <span class="badge bg-warning"><i class="fa-solid fa-edit"></i></span>
                                </button>
                            </td>
                        </tr>
                    `;
                        resultTable.append(row);
                    });

                    $("#paginationLinks").html(generatePagination(response.results));
                }
            },
            error: function (xhr) {
                let errorMessage = xhr.responseJSON?.message || "Failed to load data";
                $("#resultCount").text(0);
                $("#resultData").html(
                    `<tr><td colspan="6" class="text-center text-danger">${errorMessage}</td></tr>`
                );
                $("#paginationLinks").html("");
            }
        });
    }
    $(document).on('click', '.update-exam', function () {
        // Get the exam ID and student ID from the data attributes
        let examId = $(this).data('exam-id');
        let studentId = $(this).data('student-id');
        let f_mark = $(this).data('full-mark');

        // Get the marks from the corresponding <td> in the same row
        let marks = $(this).closest('tr').find('.marks-cell').text().trim();

        // Populate the modal with data
        $('#exam_id').val(examId);
        $('#student_id').val(studentId);
        $('#marks').val(marks);
        $('#f_mark').text('('+f_mark+')');
        let isPending = marks === "Pending";
        $('#marks').data('pending', isPending);

        // Show the modal
        $('#exampleModal').modal('show');
    });

    $('#saveChanges').on('click', function () {
        let examId = $('#exam_id').val();
        let studentId = $('#student_id').val();
        let marks = $('#marks').val();

        if (!examId || !studentId || marks === "") {
            toastr.error("Please fill in all fields.");
            return;
        }

        let url, method, data;
        let isPending = $('#marks').data('pending'); // Get the stored pending status

        if (isPending) {
            // If marks were "Pending", store a new result
            url = `http://127.0.0.1:8000/api/admin/exam-result`;
            method = "POST";
            data = { exam_id: examId, student_id: studentId, marks: marks }; // Include exam_id and student_id in the payload
        } else {
            // If marks were not "Pending", update the result
            url = `http://127.0.0.1:8000/api/admin/exam-results/${examId}/${studentId}`;
            method = "PATCH";
            data = { marks: marks }; // Only send marks for updating
        }

        $.ajax({
            url: url,
            type: method,
            data: data,
            dataType: "json",
            headers: { "Authorization": `Bearer ${adminToken}` },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    $('#exampleModal').modal('hide');
                    fetchResults(); // Refresh the results table
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                let errorMessage = xhr.responseJSON?.message || "Failed to save changes";
                toastr.error(errorMessage);
            }
        });
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
        fetchResults(page, currentSearchParams); // Use stored search params when paginating
    });
});
