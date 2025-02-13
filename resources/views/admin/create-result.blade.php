<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Upload new result')</title>
    @include('components.stylelinks')
</head>

<body class="g-sidenav-show bg-gray-100">
    @include('components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

        @section('breadcrumb', 'Result')
        @section('page-title', 'Upload new result')

        @include('components.navbar')

        <div class="container-fluid py-4">
            <form id="createResult">
                <!-- Class Selection -->
                <select class="form-select form-select-lg mb-2" id="exam" required>
                    <option selected disabled>Select an exam</option>
                </select>

                <!-- Student Selection -->
                <select class="form-select form-select-lg mb-2" id="student" required disabled>
                    <option selected disabled>Select the student</option>
                </select>

                <!-- Section Name -->
                <div class="form-group">
                    <label for="mark">Mark</label>
                    <input type="text" class="form-control" id="mark" placeholder="Enter the mark" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </main>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/soft-ui-dashboard.min.js?v=1.1.0') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @include('components.check-admin-auth')

    <script>
        $(document).ready(function() {
            $('#student').prop('disabled', true);

            // When an exam is selected, fetch students
            $('#exam').on('change', function() {
                let examId = $(this).val(); // Get selected exam ID

                if (examId) {
                    $.ajax({
                        url: 'http://127.0.0.1:8000/api/admin/exam/' + examId + '/students',
                        type: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + adminToken
                        },
                        success: function(response) {
                            console.log(response);
                            $('#student').empty().append(
                                '<option selected disabled>Select a student</option>');

                            if (response.students.length > 0) {
                                response.students.forEach(student => {
                                    $('#student').append(
                                        `<option value="${student.id}">${student.student_id}</option>`
                                    );
                                });
                                $('#student').prop('disabled', false);
                            } else {
                                $('#student').empty().append(
                                    '<option disabled>No student available for this exam</option>'
                                );
                                $('#student').prop('disabled', true);
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 404) {
                                toastr.warning(xhr.responseJSON
                                    .message); // Show 404 message in toastr
                                $('#student').empty().append(
                                    '<option disabled>No student available for this exam</option>'
                                );
                                $('#student').prop('disabled', true);
                            } else {
                                toastr.error('An error occurred while fetching students');
                            }
                        }
                    });
                } else {
                    // If no exam is selected, reset the dropdown
                    $('#student').empty().append('<option selected disabled>Select a student</option>');
                    $('#student').prop('disabled', true);
                }
            });
            let adminToken = localStorage.getItem("admin_token");
            if (!adminToken) {
                toastr.error("Unauthorized access. Admin token is missing.");
                return;
            }
            // ✅ Fetch exams and Populate Dropdown
            $.ajax({
                url: "http://127.0.0.1:8000/api/admin/exam/all",
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + adminToken

                },
                success: function(response) {
                    console.log("API Response:", response); // Log the full response

                    let examList = response.exams; // Access nested array

                    if (response.status && Array.isArray(examList)) {
                        let examDropdown = $("#exam");
                        examDropdown.empty().append(
                            '<option selected disabled>Select an exam</option>');

                        examList.forEach(exam => {
                            console.log(`Class ID: ${exam.id}, Class Name: ${exam.subject}`);
                            examDropdown.append(
                                `<option value="${exam.id}">${exam.subject}</option>`);
                        });
                    } else {
                        console.error("Error: 'exams.exams' is not an array", response.classes);
                        toastr.error("Unexpected response format.");
                    }
                },
                error: function(xhr) {
                    console.error("AJAX Error:", xhr.responseText);
                    toastr.error("Failed to load exams.");
                }
            });

            // ✅ Submit Form via AJAX
            $('#createResult').submit(function(e) {
                e.preventDefault();

                let examId = $('#exam').val();
                let studentId = $('#student').val();
                let mark = $('#mark').val().trim();

                if (!examId || !studentId || !mark) {
                    toastr.error("Please fill in all required fields.");
                    return;
                }

                $.ajax({
                    url: "http://127.0.0.1:8000/api/admin/exam-result",
                    type: 'POST',
                    headers: {
                        "Authorization": "Bearer " + adminToken
                    },
                    data: JSON.stringify({
                        exam_id: examId,
                        student_id: studentId,
                        marks: mark
                    }),
                    contentType: "application/json",
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#createResult')[0].reset();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) { // Unauthorized
                            toastr.error(
                            "Invalid or expired admin token. Please log in again.");
                            localStorage.removeItem("admin_token");
                            setTimeout(() => window.location.href = "/admin-sign-in", 2000);
                        } else if (xhr.status === 422) {
                            let errors = xhr.responseJSON.error;
                            console.log(errors); // Debugging output

                            if (errors) {
                                Object.keys(errors).forEach(field => {
                                    toastr.warning(errors[field][0]); // Show the first validation message
                                });
                            } else {
                                toastr.warning("Validation failed, but no error messages received.");
                            }
                        } else {
                            toastr.error("An unexpected error occurred.");
                        }
                    }



                });
            });

            // ✅ Show Session Messages (if any)
            let successMessage = sessionStorage.getItem("successMessage");
            if (successMessage) {
                toastr.success(successMessage);
                sessionStorage.removeItem("successMessage");
            }
        });
    </script>
</body>


</html>
