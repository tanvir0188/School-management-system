<script>
    $.ajax({
        url: `http://127.0.0.1:8000/api/studentCount`,
        type: "GET",
        dataType: "json",
        success: function(response) {
            let studentCount = response.studentCount;
            let student = $('#studentCount')
            student.text(studentCount);
            console.log(studentCount);

        },
    });
</script>
<script>
    $(document).ready(function() {
        let adminToken = localStorage.getItem("admin_token");
        let currentSearchParams = {}; // Store search parameters globally
        if (!adminToken) {
            toastr.error("Unauthorized access. Admin token is missing.");
            return;
        }



        fetchStudents();
        $("#searchButton").on("click", function(e) {
            e.preventDefault(); // Prevents form reload

            let searchValue = $("#searchStudents").val().trim();

            currentSearchParams = {
                search: searchValue
            }; // Store search params globally

            fetchStudents(1, currentSearchParams);
        });




        function fetchStudents(page = 1, searchParams = {}) {
            let adminToken = localStorage.getItem("admin_token");

            $.ajax({
                url: `http://127.0.0.1:8000/api/students/search?page=${page}`,
                type: "GET",
                data: searchParams,
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + adminToken
                },
                success: function(response) {
                    if (response.status) {
                        let students = response.data.data; // Access nested data
                        let studentTable = $("#studentData");
                        studentTable.empty();

                        $.each(students, function(index, student) {
                            let row = `
                        <tr>
                            <td>${student.name}</td>
                            <td>${student.student_id}</td>
                            <td>${student.email}</td>
                            <td class="text-center"><span class="badge bg-primary">${student.class_id}</span></td>
                            <td class="text-center"><span class="badge bg-secondary">${student.sec_id}</span></td>
                            <td class="text-center">
                                <a href="#" class="text-danger delete-student" data-id="${student.id}">
                                    <span class="badge bg-danger"><i class="fa-solid fa-trash"></i></span>
                                </a>
                            </td>
                        </tr>
                    `;
                            studentTable.append(row);
                        });

                        $("#paginationLinks").html(generatePagination(response.data));
                    } else {
                        $("#studentData").html(
                            `<tr><td colspan="5" class="text-center text-danger">${response.message}</td></tr>`
                        );
                        $("#paginationLinks").html("");
                    }
                },
                error: function() {
                    $("#studentData").html(
                        `<tr><td colspan="5" class="text-center text-danger">Failed to load data</td></tr>`
                    );
                    $("#paginationLinks").html("");
                }
            });
        }


        function deleteStudent(studentId) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/admin/student/${studentId}`,
                type: "DELETE",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + adminToken
                },
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message); // Show success message
                        fetchStudents(); // Refresh the table data
                    } else {
                        toastr.error(response.message); // Show error message
                    }
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON?.message || "Failed to delete student";
                    toastr.error(errorMessage); // Show error message
                }
            });
        }
        $(document).on("click", ".delete-student", function(e) {
            e.preventDefault(); // Prevent default link behavior
            let studentId = $(this).data("id"); // Get the student ID
            if (confirm("Are you sure you want to delete this student?")) {
                deleteStudent(studentId); // Call the delete function
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
        fetchStudents(page, currentSearchParams); // Use stored search params when paginating
    });
    });
</script>
