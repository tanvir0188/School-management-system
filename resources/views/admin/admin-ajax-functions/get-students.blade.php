<script>
    $.ajax({
            url: `http://127.0.0.1:8000/api/studentCount`,
            type: "GET",
            dataType: "json",
            success: function(response) {
                let studentCount = response.studentCount;
                let student = $('#studentCount')
                student.text(studentCount);
                console.log( studentCount );
                
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

        

        fetchStudents();
        

        function fetchStudents(page = 1) {
            // Fixed URL string interpolation using backticks
            $.ajax({
                url: `http://127.0.0.1:8000/api/admin/student/index?page=${page}`,
                type: "GET",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + adminToken
                },
                success: function(response) {
                    if (response.status) {
                        // Corrected data access path
                        let students = response.students.students.data;
                        let studentTable = $("#studentData");
                        studentTable.empty();

                        $.each(students, function(index, student) {
                            let row = `
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">${student.name}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-xs">${student.email}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-primary">${student.class_id}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-secondary">${student.sec_id}</span>
                                    </td>
                                </tr>
                            `;
                            studentTable.append(row);
                        });
                        // Pass the correct pagination data
                        $("#paginationLinks").html(generatePagination(response.students.students));
                    } else {
                        $("#studentData").html(
                            `<tr><td colspan="4" class="text-center text-danger">${response.message}</td></tr>`
                        );
                        $("#paginationLinks").html("");
                    }
                },
                error: function() {
                    $("#studentData").html(
                        `<tr><td colspan="4" class="text-center text-danger">Failed to load data</td></tr>`
                    );
                    $("#paginationLinks").html("");
                }
            });
        }

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
            fetchStudents(page);
        });
    });
</script>