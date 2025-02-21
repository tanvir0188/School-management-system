$(document).ready(function () {
    let teacherToken = localStorage.getItem("teacher_token");

    if (!teacherToken) {
        toastr.error("Unauthorized access. Teacher token is missing.");
        return;
    }

    // Extract sectionId from the URL
    let url = window.location.href;
    let urlParts = url.split('/');
    let sectionId = urlParts[urlParts.length - 1];

    let currentSectionId = sectionId;
    if (!currentSectionId) {
        toastr.error("Section ID is missing.");
        return;
    }

    fetchSectionNotices(currentSectionId);

    function fetchSectionNotices(sectionId, page = 1) {
        $.ajax({
            url: `http://127.0.0.1:8000/api/teacher/section-notices/${sectionId}?page=${page}`,
            type: "GET",
            dataType: "json",
            headers: {
                "Authorization": "Bearer " + teacherToken
            },
            success: function (response) {
                if (response.status) {
                    // Assuming the paginated notices data is returned in response.notices
                    let notices = response.notices.data;
                    
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
                                    <td class="text-center">
                                        <span class="text-xs">${formattedDate}</span>
                                    </td>
                                    <td>
                                        <button class="text-danger delete-notice" data-id="${notice.id}" style="border: none; background: none; padding: 0;">
                                            <span class="badge bg-danger"><i class="fa-solid fa-trash"></i></span>
                                        </button>
                                        <button class="text-warning update-notice" data-id="${notice.id}" style="border: none; background: none; padding: 0;">
                                            <span class="badge bg-warning"><i class="fa-solid fa-edit"></i></span>
                                        </button>
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

    $(document).on('click', '#createSectionNotice', function () {

        $('#sec_id').val(sectionId);
        // Show the modal
        $('#createNotice').modal('show');
    });
    $(document).on("click", "#create", function (e) {
        e.preventDefault();

        let secId = $("#sec_id").val();
        let title = $("#title").val();
        let content = $("#content").val();

        if (!title || !content) {
            toastr.error("Title and content are required!");
            return;
        }

        $.ajax({
            url: "http://127.0.0.1:8000/api/teacher/section-notice/store",
            type: "POST",
            dataType: "json",
            contentType: "application/json",
            headers: {
                Authorization: `Bearer ${teacherToken}`,
            },
            data: JSON.stringify({
                sec_id: secId,
                title: title,
                content: content,
            }),
            success: function (response) {
                
                if (response.status) {
                    toastr.success(response.message);
                    $("#createNotice").modal("hide"); // Close the modal after success
                    $("#createNoticeForm")[0].reset(); // Clear form fields
                    fetchSectionNotices(currentSectionId, 1);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {

                let errorMessage = "Failed to post notice.";
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                toastr.error(errorMessage);
            },
        });
    });

    $(document).on("click", ".update-notice", function (e) {
        e.preventDefault(); // Prevent default button behavior
    
        const noticeId = $(this).data("id"); // Get the notice ID from the data-id attribute   
        // Fetch the notice details
        $.ajax({
            url: `http://127.0.0.1:8000/api/section-notice/${noticeId}`,
            type: "GET",
            success: function (response) {
                if (response.status) {
                    // Populate the form with the notice data
                    $("#updateNoticeForm #title").val(response.sectionNotice.title);
                    $("#updateNoticeForm #content").val(response.sectionNotice.content);
                    $("#updateNoticeForm #sec_id").val(currentSectionId);
    
                    // Store the notice ID in a hidden field for later use
                    $("#updateNoticeForm").append(`<input type="hidden" id="notice_id" name="notice_id" value="${noticeId}">`);
    
                    // Open the modal
                    $("#updateNotice").modal("show");
                } else {
                    toastr.error(response.error || "Failed to fetch notice details.");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                let errorMessage = "Failed to fetch notice details. Please try again later.";
    
                // Check if the response contains a JSON message
                if (jqXHR.responseJSON && jqXHR.responseJSON.error) {
                    errorMessage = jqXHR.responseJSON.error;
                }
    
                toastr.error(errorMessage);
            },
        });
    });

    $(document).on("click", "#update", function (e) {
        e.preventDefault(); 
        const noticeId = $("#updateNoticeForm #notice_id").val();
      
        // Get form data
        const formData = {
            sec_id: $("#updateNoticeForm #sec_id").val(),
            title: $("#updateNoticeForm #title").val(),
            content: $("#updateNoticeForm #content").val(),
        };
    
        // Send the AJAX request to update the notice
        $.ajax({
            url: `http://127.0.0.1:8000/api/teacher/section-notice/${noticeId}`,
            type: "PUT",
            headers: {
                "Authorization": "Bearer " + teacherToken,
                "Content-Type": "application/json",
            },
            data: JSON.stringify(formData),
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message || "Notice updated successfully.");
                    $("#updateNotice").modal("hide"); // Close the modal
                    fetchSectionNotices(currentSectionId); // Refresh the notices list
                } else {
                    toastr.error(response.error || "Failed to update notice.");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                let errorMessage = "Failed to update notice. Please try again later.";
    
                // Check if the response contains a JSON message
                if (jqXHR.responseJSON && jqXHR.responseJSON.error) {
                    errorMessage = jqXHR.responseJSON.error;
                }
    
                toastr.error(errorMessage);
            },
        });
    });


    function deleteSectionNotice(noticeId) {
        // Confirm deletion with the user
        if (!confirm("Are you sure you want to delete this notice?")) {
            return;
        }

        $.ajax({
            url: `http://127.0.0.1:8000/api/teacher/section-notice/${noticeId}`,
            type: "DELETE",
            headers: {
                "Authorization": "Bearer " + teacherToken,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message || "Notice deleted successfully.");
                    // Refresh the notices list after deletion
                    fetchSectionNotices(currentSectionId);
                } else {
                    toastr.error(response.error || "Failed to delete notice.");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                let errorMessage = "Failed to delete notice. Please try again later.";

                // Check if the response contains a JSON message
                if (jqXHR.responseJSON && jqXHR.responseJSON.error) {
                    errorMessage = jqXHR.responseJSON.error;
                }

                toastr.error(errorMessage);
            },
        });
    }
    $(document).on("click", ".delete-notice", function (e) {
        e.preventDefault(); // Prevent default button behavior
        const noticeId = $(this).data("id"); // Get the notice ID from the data-id attribute
        deleteSectionNotice(noticeId); // Call the delete function
    });


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
                    let notice = response.sectionNotice; // or response.notice based on API response
                    let formattedDate = new Date(notice.created_at).toISOString().split("T")[0];

                    // Update modal content
                    let noticeHtml = `
                        <h4 class="text-primary">${notice.title}</h4>
                        <p>${notice.content}</p>
                        <small class="text-muted">Posted on: ${formattedDate}</small>
                    `;

                    $("#showNotice .modal-body").html(noticeHtml);

                    // Show the modal
                    $("#showNotice").modal("show");
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