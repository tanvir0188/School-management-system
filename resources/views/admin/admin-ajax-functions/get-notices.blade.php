<script>
    $.ajax({
        url: `http://127.0.0.1:8000/api/noticeCount`,
        type: "GET",
        dataType: "json",
        success: function(response) {
            let noticeCount = response.noticeCount;
            let notice = $('#noticeCount')
            notice.text(noticeCount);
            console.log(noticeCount);

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



        fetchNotices();


        function fetchNotices(page = 1) {
            // Fixed URL string interpolation using backticks
            $.ajax({
                url: `http://127.0.0.1:8000/api/notice?page=${page}`,
                type: "GET",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + adminToken
                },
                success: function(response) {
                    if (response.status) {
                        // Corrected data access path
                        let notices = response.notices.notices.data;
                        let noticeTable = $("#noticeData");
                        noticeTable.empty();

                        $.each(notices, function(index, notice) {
                            let formattedDate = new Date(notice.created_at).toISOString().split("T")[0];
                            let row = `
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <a class="text-dark show-notice" href="#" data-id="${notice.id}">
                                                <h6 class="mb-0 text-sm" data-toggle="tooltip" data-placement="right" title="Show notice">${notice.title}</h6>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-xs">${formattedDate}</span>
                                    </td>
                                    <td>
                                        <a href="#" class="text-danger delete-notice" data-id="${notice.id}">
                                            <span class="badge bg-danger"><i class="fa-solid fa-trash"></i></span>
                                        </a>
                                    </td>
                                </tr>
                            `;
                            noticeTable.append(row);
                        });
                        // Pass the correct pagination data
                        $("#paginationLinks").html(generatePagination(response.notices.notices));
                    } else {
                        $("#noticeData").html(
                            `<tr><td colspan="4" class="text-center text-danger">${response.message}</td></tr>`
                        );
                        $("#paginationLinks").html("");
                    }
                },
                error: function() {
                    $("#noticeData").html(
                        `<tr><td colspan="4" class="text-center text-danger">Failed to load data</td></tr>`
                    );
                    $("#paginationLinks").html("");
                }
            });
        }

        function showNotice(noticeId) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/notice/${noticeId}`,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    console.log(response.notice)
                    if (response.status) {
                        let notice = response.notice;
                        let formattedDate = new Date(notice.created_at).toISOString().split("T")[0];
                        let noticeHtml = `
                        <div class="card shadow-sm p-3">
                            <h4 class="text-primary">${notice.title}</h4>
                            <p>${notice.content}</p>
                            <small class="text-muted">Posted on: ${formattedDate}</small>
                        </div>
                    `;
                        $("#showNotice").html(noticeHtml);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error("Failed to load notice details.");
                }
            });
        }
        $(document).on("click", ".show-notice", function(e) {
            e.preventDefault();
            let noticeId = $(this).data("id");
            showNotice(noticeId);
        });

        function deleteNotice(noticeId) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/admin/notice/${noticeId}`,
                type: "DELETE",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + adminToken
                },
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message); // Show success message
                        fetchNotices(); // Refresh the table data
                    } else {
                        toastr.error(response.message); // Show error message
                    }
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON?.message || "Failed to delete notice";
                    toastr.error(errorMessage); // Show error message
                }
            });
        }
        $(document).on("click", ".delete-notice", function(e) {
            e.preventDefault(); // Prevent default link behavior
            let noticeId = $(this).data("id");
            if (confirm("Are you sure you want to delete this notice?")) {
                deleteNotice(noticeId); // Call the delete function
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
            fetchNotices(page);
        });
    });
</script>
