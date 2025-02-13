<script>
    $(document).ready(function() {
        let adminToken = localStorage.getItem("admin_token");
        let currentSearchParams = {}; // Store search parameters globally
        if (!adminToken) {
            toastr.error("Unauthorized access. Admin token is missing.");
            return;
        }



        fetchExams();
        $("#searchButton").on("click", function(e) {
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

                success: function(response) {
                    if (response.status) {
                        let exams = response.exams.data; // Access nested data
                        let examCount = response.examCount;
                        let searchedExam = $('#examCount');
                        searchedExam.text(examCount);
                        console.log('count:' + examCount);

                        let examTable = $("#examData");
                        examTable.empty();

                        $.each(exams, function(index, exam) {
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
                            </td>
                        </tr>
                    `;
                            examTable.append(row);
                        });

                        $("#paginationLinks").html(generatePagination(response.exams));
                    }
                },
                error: function(xhr) {
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
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message); // Show success message
                        fetchExams(); // Refresh the table data
                    } else {
                        toastr.error(response.message); // Show error message
                    }
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON?.message || "Failed to delete exam";
                    toastr.error(errorMessage); // Show error message
                }
            });
        }
        $(document).on("click", ".delete-exam", function(e) {
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

        $(document).on("click", "#paginationLinks .page-link", function(e) {
            e.preventDefault();
            let page = $(this).data("page");
            fetchExams(page, currentSearchParams); // Use stored search params when paginating
        });
    });
</script>
