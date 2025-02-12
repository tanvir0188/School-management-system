<script>
    $.ajax({
        url: `http://127.0.0.1:8000/api/exam-type-count`,
        type: "GET",
        dataType: "json",
        success: function(response) {
            let examTypeCount = response.examTypeCount;
            let examType = $('#examTypeCount')
            examType.text(examTypeCount);
            console.log(examTypeCount);

        },
    });
</script>
<script>
    $(document).ready(function() {
        let adminToken = localStorage.getItem("admin_token");

        if (!adminToken) {
            toastr.error("Unauthorized access. Admin token is missing.");
            return;
        }

        fetchExamTypeByCount();

        function fetchExamTypeByCount() {
            $.ajax({
                url: `http://127.0.0.1:8000/api/exam-count-by-type`,
                type: "GET",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + adminToken
                },
                success: function(response) {
                    if (response.status) {
                        // Access the exam type data
                        let examTypes = response.data;
                        let examTypeTable = $("#examTypeData");
                        examTypeTable.empty(); // Clear existing data

                        // Loop through the exam types and append rows to the table
                        $.each(examTypes, function(index, examType) {
                            let row = `
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <a class="text-dark show-detail" href="#" data-id="${examType.id}">
                                                <h6 class="mb-0 text-sm" data-toggle="tooltip" data-placement="right" title="See all the ${examType.name} details">${examType.name}</h6>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-xs">
                                        <span class="badge badge-sm bg-primary">${examType.exam_count}</span>
                                    </td>
                                    <td class="text-xs">
                                        <a href="#" class="text-secondary text-xs delete-examType" data-id="${examType.id}">
                                            <span class="badge badge-sm bg-danger"><i class="fa-solid fa-trash"></i></span>
                                        </a>
                                    </td>
                                </tr>
                            `;
                            examTypeTable.append(row);
                        });
                    } else {
                        // Handle case where no data is found
                        $("#examTypeData").html(
                            `<tr><td colspan="3" class="text-center text-danger">${response.message}</td></tr>`
                        );
                    }
                },
                error: function(xhr) {
                    // Handle AJAX errors
                    let errorMessage = xhr.responseJSON?.message || "Failed to load data";
                    $("#examTypeData").html(
                        `<tr><td colspan="3" class="text-center text-danger">${errorMessage}</td></tr>`
                    );
                }
            });
        }

        $(document).on("click", ".show-detail", function(e) {
            e.preventDefault();
            let examTypeId = $(this).data("id");
            fetchExamDetails(examTypeId, 1); // Fetch page 1 initially
        });

        function fetchExamDetails(examTypeId, page = 1) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/admin/exam-result-by-type/${examTypeId}?page=${page}`,
                type: "GET",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + adminToken
                },
                success: function(response) {
                    let examDetailsTable = $("#examDetailData");
                    examDetailsTable.empty();

                    if (response.status) {
                        setTimeout(function() {
                            let examCount = $("#examResultCount");
                            examCount.text(response
                            .examResultsCount); // Set the exam results count after a short delay
                        }, 100);

                        $.each(response.data.data, function(index, exam) {
                            let row = `
                    <tr>
                        <td>${exam.exam_date}</td>
                        <td>${exam.class_name}</td>
                        <td>${exam.student_name}</td>
                        <td>${exam.student_id}</td>
                        <td>${exam.subject}</td>
                        <td>${exam.full_marks}</td>
                        <td>${exam.mark}</td>
                    </tr>
                `;
                            examDetailsTable.append(row);
                        });

                        $("#paginationLinks").html(generatePagination(response.data, examTypeId));
                        $("#examDetailCard").removeClass("invisible");

                    } else {
                        toastr.warning(response.message, "Warning");
                        examDetailsTable.html(
                            `<tr><td colspan="7" class="text-center text-danger">${response.message}</td></tr>`
                        );
                        $("#paginationLinks").html("");
                    }
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON?.message || "Failed to load exam details";
                    toastr.error(errorMessage, "Error");
                    $("#examDetailData").html(
                        `<tr><td colspan="7" class="text-center text-danger">${errorMessage}</td></tr>`
                    );
                    $("#paginationLinks").html("");
                }
            });
        }


        // Delete exam type event listener
        $(document).on("click", ".delete-examType", function(e) {
            e.preventDefault(); // Prevent default link behavior

            let examTypeId = $(this).data("id"); // Get the exam type ID from the data attribute

            if (confirm("Are you sure you want to delete this exam type?")) {
                deleteExamType(examTypeId); // Call the delete function
            }
        });

        // Delete exam type function
        function deleteExamType(examTypeId) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/admin/exam-type/${examTypeId}`,
                type: "DELETE",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + adminToken
                },
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message); // Show success message
                        fetchExamTypeByCount(); // Refresh the table data
                    } else {
                        toastr.error(response.message); // Show error message
                    }
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON?.message || "Failed to delete exam type";
                    toastr.error(errorMessage); // Show error message
                }
            });
        }

        function generatePagination(data, examTypeId) {
            let paginationHtml = '<nav><ul class="pagination justify-content-center">';

            // Previous link
            if (data.prev_page_url) {
                paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${data.current_page - 1}" data-exam-type="${examTypeId}">&laquo;</a>
                </li>`;
            }

            // Page numbers
            for (let i = 1; i <= data.last_page; i++) {
                paginationHtml += `
                <li class="page-item ${i === data.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}" data-exam-type="${examTypeId}">${i}</a>
                </li>`;
            }

            // Next link
            if (data.next_page_url) {
                paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${data.current_page + 1}" data-exam-type="${examTypeId}">&raquo;</a>
                </li>`;
            }

            paginationHtml += '</ul></nav>';
            return paginationHtml;
        }
        $(document).on("click", "#paginationLinks .page-link", function(e) {
            e.preventDefault();
            let page = $(this).data("page");
            let examTypeId = $(this).data("exam-type");
            fetchExamDetails(examTypeId, page);
        });
    });
</script>
