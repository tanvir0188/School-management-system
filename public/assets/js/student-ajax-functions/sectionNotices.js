$(document).ready(function () {
    let studentToken = localStorage.getItem("student_token");

    if (!studentToken) {
        toastr.error("Unauthorized access. Student token is missing.");
        return;
    }

    // Initially load section notices (for example, for the section stored in localStorage)
    // You might store the current section ID somewhere; here we assume it's stored as 'currentSectionId'
    let student = localStorage.getItem("student");
    student = JSON.parse(student);
    let currentSectionId = student.sec_id;
    if (!currentSectionId) {
        toastr.error("Section ID is missing.");
        return;
    }
    
    fetchSectionNotices(currentSectionId);

    function fetchSectionNotices(sectionId, page = 1) {
        $.ajax({
            url: `http://127.0.0.1:8000/api/student/section-notices/${sectionId}?page=${page}`,
            type: "GET",
            dataType: "json",
            headers: {
                "Authorization": "Bearer " + studentToken
            },
            success: function (response) {
                if (response.status) {
                    // Assuming the paginated notices data is returned in response.notices
                    let notices = response.notices.data;
                    console.log(response);
                    // If you have a notice count, update a designated element (e.g., #sectionNoticeCount)
                    $("#sectionNoticeCount").text(response.noticeCount);

                    let noticeTable = $("#sectionNoticeData");
                    noticeTable.empty();

                    $.each(notices, function (index, notice) {
                        let formattedDate = new Date(notice.created_at).toISOString().split("T")[0];
                        let row = `
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <a class="text-dark show-section-notice" href="#" data-id="${notice.id}">
                                                <h6 class="mb-0 text-sm" data-toggle="tooltip" data-placement="right" title="Show notice">${notice.title}</h6>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-xs">${formattedDate}</span>
                                </td>
                            </tr>
                        `;
                        noticeTable.append(row);
                    });

                    // Render pagination using a helper function (see below)
                    $("#sectionNoticePaginationLinks").html(generatePagination(response.notices));
                } else {
                    $("#sectionNoticeData").html(
                        `<tr><td colspan="2" class="text-center text-danger">${response.message}</td></tr>`
                    );
                    $("#sectionNoticePaginationLinks").html("");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                let errorMessage = "Failed to load data. Please try again later.";
            
                // Check if the response contains a JSON message
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    errorMessage = jqXHR.responseJSON.message;
                }
            
                $("#sectionNoticeData").html(
                    `<tr><td colspan="2" class="text-center text-danger">${errorMessage}</td></tr>`
                );
                $("#sectionNoticePaginationLinks").html("");
            }
            
            
        });
    }

    // Simple pagination generator (adjust as needed)
    function generatePagination(data) {
        let paginationHtml = '<nav><ul class="pagination justify-content-center">';

        // Previous page link
        if (data.prev_page_url) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${data.current_page - 1}" title="Previous"><</a>
                </li>`;
        }

        // Current page indicator (could be extended to show page numbers)

        // Next page link
        if (data.next_page_url) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${data.current_page + 1}" title="Next">></a>
                </li>`;
        }

        paginationHtml += '</ul></nav>';
        return paginationHtml;
    }

    // Handle pagination link clicks for section notices
    $(document).on("click", "#sectionNoticePaginationLinks .page-link", function (e) {
        e.preventDefault();
        let page = $(this).data("page");
        fetchSectionNotices(currentSectionId, page);
    });

    // Handle click on a notice to show its details (using your showNotice function as an example)
    $(document).on("click", ".show-section-notice", function (e) {
        e.preventDefault();
        let noticeId = $(this).data("id");
        showNotice(noticeId);
    });

    // Example function for showing a notice's details (you can adjust as needed)
    function showNotice(noticeId) {
        $.ajax({
            url: `http://127.0.0.1:8000/api/section-notice/${noticeId}`,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.status) {
                    let notice = response.sectionNotice; // or response.notice based on your API
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
            error: function () {
                toastr.error("Failed to load notice details.");
            }
        });
    }
});
