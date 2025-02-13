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




    function fetchResults(page = 1, searchParams = {}) {

        $.ajax({
            url: `http://127.0.0.1:8000/api/admin/exam-results?page=${page}`,
            type: "GET",
            data: searchParams,
            dataType: "json",
            headers: {
                "Authorization": `Bearer ${adminToken}`
            },

            success: function (response) {
                if (response.status) {
                    let results = response.results.data; // Access nested data
                    let resultCount = response.resultCount;
                    let searchedResult = $('#resultCount');
                    searchedResult.text(resultCount);
                    console.log('count:' + resultCount);

                    let resultTable = $("#resultData");
                    resultTable.empty();

                    $.each(results, function (index, result) {
                        let row = `
                        <tr>
                            <td>${result.exam_type_name}</td>
                            <td>${result.subject}</td>
                            <td>${result.class_name}</td>
                            <td>${result.section_name}</td>
                            <td>${result.student_name}</td>
                            <td>${result.student_id}</td>
                            <td><span class="badge bg-primary">${result.full_marks}</span></td>
                            <td><span class="badge bg-warning">${result.marks}</span></td>
                            <td><span class="badge bg-secondary">${result.exam_date}</span></td>
                            <td>
                                <a href="#" class="text-warning update-exam" data-id="${result.exam_id}">
                                    <span class="badge bg-warning"><i class="fa-solid fa-edit"></i></span>
                                </a>
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


    function deleteResult(resultId) {
        $.ajax({
            url: `http://127.0.0.1:8000/api/admin/exam-result/${resultId}`,
            type: "DELETE",
            dataType: "json",
            headers: {
                "Authorization": "Bearer " + adminToken
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message); // Show success message
                    fetchResults(); // Refresh the table data
                } else {
                    toastr.error(response.message); // Show error message
                }
            },
            error: function (xhr) {
                let errorMessage = xhr.responseJSON?.message || "Failed to delete result";
                toastr.error(errorMessage); // Show error message
            }
        });
    }
    $(document).on("click", ".delete-result", function (e) {
        e.preventDefault(); // Prevent default link behavior
        let resultmId = $(this).data("id"); // Get the exam ID
        if (confirm("Are you sure you want to delete this result?")) {
            deleteResult(resultId); // Call the delete function
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

    $(document).on("click", "#paginationLinks .page-link", function (e) {
        e.preventDefault();
        let page = $(this).data("page");
        fetchResults(page, currentSearchParams); // Use stored search params when paginating
    });
});
