$(document).ready(function () {
    let studentToken = localStorage.getItem("student_token");

    if (!studentToken) {
        toastr.error("Unauthorized access. Student token is missing.");
        return;
    }



    fetchSchoolNotices();
    


    function fetchSchoolNotices(page = 1) {
        // Fixed URL string interpolation using backticks
        $.ajax({
            url: `http://127.0.0.1:8000/api/notice?page=${page}`,
            type: "GET",
            dataType: "json",
            headers: {
                "Authorization": "Bearer " + studentToken
            },
            success: function (response) {
                if (response.status) {
                    // Corrected data access path
                    let notices = response.notices.notices.data;
                    $("#schoolNoticeCount").text(response.noticeCount);
                    let noticeTable = $("#schoolNoticeData");
                    noticeTable.empty();

                    $.each(notices, function (index, notice) {
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
                            </tr>
                        `;
                        noticeTable.append(row);
                    });
                    // Pass the correct pagination data
                    $("#schoolNoticePaginationLinks").html(generatePagination(response.notices.notices));
                } else {
                    $("#noticeData").html(
                        `<tr><td colspan="4" class="text-center text-danger">${response.message}</td></tr>`
                    );
                    $("#schoolNoticePaginationLinks").html("");
                }
            },
            error: function () {
                $("#noticeData").html(
                    `<tr><td colspan="4" class="text-center text-danger">Failed to load data</td></tr>`
                );
                $("#schoolNoticePaginationLinks").html("");
            }
        });
    }

    function showNotice(noticeId) {
        $.ajax({
            url: `http://127.0.0.1:8000/api/notice/${noticeId}`,
            type: "GET",
            dataType: "json",
            success: function (response) {
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
            error: function () {
                toastr.error("Failed to load notice details.");
            }
        });
    }
    $(document).on("click", ".show-notice", function (e) {
        e.preventDefault();
        let noticeId = $(this).data("id");
        showNotice(noticeId);
    });

    

    function generatePagination(data) {
        let paginationHtml = '<nav><ul class="pagination justify-content-center">';

        // Previous link
        if (data.prev_page_url) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" title="Previous" href="#" data-page="${data.current_page - 1}"><</a>
                </li>`;
        }

        

        // Next link
        if (data.next_page_url) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" title="Next" data-page="${data.current_page + 1}">></a>
                </li>`;
        }

        paginationHtml += '</ul></nav>';
        return paginationHtml;
    }

    $(document).on("click", "#schoolNoticePaginationLinks .page-link", function (e) {
        e.preventDefault();
        let page = $(this).data("page");
        fetchSchoolNotices(page);
    });
});