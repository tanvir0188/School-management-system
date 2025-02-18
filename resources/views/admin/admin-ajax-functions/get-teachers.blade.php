<script>
    $.ajax({
        url: `http://127.0.0.1:8000/api/teacherCount`,
        type: "GET",
        dataType: "json",
        success: function(response) {
            let teacherCount = response.teacherCount;
            let teacher = $('#teacherCount')
            teacher.text(teacherCount);
            console.log(teacherCount);

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



        fetchTeachers();


        function fetchTeachers(page = 1) {
            // Fixed URL string interpolation using backticks
            $.ajax({
                url: `http://127.0.0.1:8000/api/admin/teacher/index?page=${page}`,
                type: "GET",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + adminToken
                },
                success: function(response) {
                    if (response.status) {
                        // Corrected data access path
                        let teachers = response.teachers.teachers.data;
                        let teacherTable = $("#teacherData");
                        teacherTable.empty();

                        $.each(teachers, function(index, teacher) {
                            let row = `
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">${teacher.name}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-xs">${teacher.email}</span>
                                    </td>
                                    <td class="text-xs">
                                        <a href="#" class="text-danger delete-teacher" data-id="${teacher.id}">
                                            <span class="badge bg-danger"><i class="fa-solid fa-trash"></i></span>
                                        </a>
                                    </td>
                                </tr>
                            `;
                            teacherTable.append(row);
                        });
                        // Pass the correct pagination data
                        $("#paginationLinks").html(generatePagination(response.teachers.teachers));
                    } else {
                        $("#teacherData").html(
                            `<tr><td colspan="4" class="text-center text-danger">${response.message}</td></tr>`
                        );
                        $("#paginationLinks").html("");
                    }
                },
                error: function() {
                    $("#teacherData").html(
                        `<tr><td colspan="4" class="text-center text-danger">Failed to load data</td></tr>`
                    );
                    $("#paginationLinks").html("");
                }
            });
        }
        function deleteTeacher(teacherId) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/admin/teacher/${teacherId}`,
                type: "DELETE",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + adminToken
                },
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message); // Show success message
                        fetchTeachers(); // Refresh the table data
                    } else {
                        toastr.error(response.message); // Show error message
                    }
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON?.message || "Failed to delete teacher";
                    toastr.error(errorMessage); // Show error message
                }
            });
        }
        $(document).on("click", ".delete-teacher", function(e) {
            e.preventDefault(); // Prevent default link behavior
            let teacherId = $(this).data("id"); // Get the student ID
            if (confirm("Are you sure you want to delete this teacher?")) {
                deleteTeacher(teacherId); // Call the delete function
            }
        });

        function generatePagination(data) {
            let paginationHtml = '<nav><ul class="pagination justify-content-center">';

            // Previous link
            if (data.prev_page_url) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="${data.current_page - 1}"><</a>
                    </li>`;
            }

            // Page numbers
            for (let i = 1; i <= data.last_page; i++) {
                paginationHtml += `
                    <li class="page-item ${i === data.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`;
            }

            // Next link
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
            fetchTeachers(page);
        });
    });
</script>
