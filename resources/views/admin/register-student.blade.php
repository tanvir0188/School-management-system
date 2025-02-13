<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Create new student account')</title>
    @include('components.stylelinks')
</head>

<body class="g-sidenav-show bg-gray-100">
    @include('components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

        @section('breadcrumb', 'Student')

        @section('page-title', 'Create New Student')
        @include('components.navbar')
        <div class="container-fluid py-4">
            <form id="registerStudent">
                <div class="form-group">

                    <label for="name">User Name</label>
                    <input type="text" class="form-control" id="name" aria-describedby="nameHelp"
                        placeholder="Enter username" required>
                </div>
                <div class="form-group">

                    <label for="student_id">Student Id</label>
                    <input type="text" class="form-control" id="studentId" aria-describedby="emailHelp"
                        placeholder="Enter student id" required>
                </div>
                <div class="form-group">

                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" class="form-control" id="email" aria-describedby="emailHelp"
                        placeholder="Enter email" required>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Password" required>
                </div>

                <div class="form-group">
                    <label for="class">Class</label>
                    <select class="form-select form-select-lg mb-2" id="className" required>
                        <option selected disabled>Select a class</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="section">Section</label>
                    <select class="form-select form-select-lg mb-2" id="section" required disabled>
                        <option selected disabled>Select a section</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

        </div>

    </main>
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
            $('#section').prop('disabled', true);

            // When a class is selected, fetch sections
            $('#className').on('change', function() {
                let classId = $(this).val(); // Get selected class ID

                if (classId) {
                    $.ajax({
                        url: 'http://127.0.0.1:8000/api/section/index-by-class/' + classId,
                        type: 'GET',
                        success: function(response) {
                            $('#section').empty().append(
                                '<option selected disabled>Select a section</option>');

                            if (response.sections.length > 0) {
                                // Populate the section dropdown with available sections
                                response.sections.forEach(section => {
                                    $('#section').append(
                                        `<option value="${section.id}">${section.name}</option>`
                                        );
                                });
                                $('#section').prop('disabled',
                                false); // Enable if sections are available
                            } else {
                                // No sections available
                                $('#section').empty().append(
                                    '<option disabled>No section available for this class</option>'
                                    );
                                $('#section').prop('disabled',
                                true); // Disable again if no sections found
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 404) {
                                toastr.warning(xhr.responseJSON.message); // Show 404 message in toastr
                                $('#section').empty().append(
                                    '<option disabled>No section available for this class</option>'
                                );
                                $('#section').prop('disabled', true);
                            } else {
                                toastr.error('An error occurred while fetching sections');
                            }
                        }
                    });
                } else {
                    // If no class is selected, reset and disable section dropdown
                    $('#section').empty().append('<option selected disabled>Select a section</option>');
                    $('#section').prop('disabled', true);
                }
            });

            $.ajax({
                url: "http://127.0.0.1:8000/api/class/index",
                type: 'GET',
                success: function(response) {
                    console.log("API Response:", response); // Log the full response

                    let classList = response.classes.classes; // Access nested array

                    if (response.status && Array.isArray(classList)) {
                        let classDropdown = $("#className");
                        classDropdown.empty().append(
                            '<option selected disabled>Select a class</option>');

                        classList.forEach(cls => {
                            console.log(`Class ID: ${cls.id}, Class Name: ${cls.name}`);
                            classDropdown.append(
                                `<option value="${cls.id}">${cls.name}</option>`);
                        });
                    } else {
                        console.error("Error: 'classes.classes' is not an array", response.classes);
                        toastr.error("Unexpected response format.");
                    }
                },
                error: function(xhr) {
                    console.error("AJAX Error:", xhr.responseText);
                    toastr.error("Failed to load classes.");
                }
            });
            $('#registerStudent').submit(function(e) {
                e.preventDefault();

                let email = $('#email').val().trim();
                let password = $('#password').val().trim();
                let username = $('#name').val().trim();
                let studentId = $('#studentId').val().trim();
                let className = $('#className').val().trim();
                let section = $('#section').val().trim();
                let adminToken = localStorage.getItem(
                    "admin_token"); // Retrieve admin token from localStorage

                if (!adminToken) {
                    toastr.error("Unauthorized access. Admin token is missing.");
                    return;
                }

                $.ajax({
                    url: "http://127.0.0.1:8000/api/admin/register/student",
                    type: 'POST',
                    headers: {
                        "Authorization": "Bearer " +
                            adminToken // Attach token in the request headers
                    },
                    data: JSON.stringify({
                        email: email,
                        password: password,
                        name: username,
                        student_id: studentId,
                        class_id: className,
                        sec_id: section,
                    }),
                    contentType: "application/json",
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#registerStudent')[0].reset();
                        } else {
                            toastr.error(response.errors);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) { // Unauthorized access
                            toastr.error(
                                "Invalid or expired admin token. Please log in again.");
                            localStorage.removeItem("admin_token"); // Remove invalid token
                            setTimeout(function() {
                                window.location.href =
                                    "/admin/login"; // Redirect to login page
                            }, 2000);
                        } else {
                            let errors = xhr.responseJSON;
                            if (errors && errors.errors) {
                                errors.errors.forEach(function(error) {
                                    toastr.error(error);
                                });
                            } else {
                                toastr.error("An unexpected error occurred. Please try again.");
                            }
                        }
                    }
                });
            });
        });
    </script>




    <script>
        $(document).ready(function() {
            // ✅ Retrieve the message stored in sessionStorage
            let successMessage = sessionStorage.getItem("successMessage");

            if (successMessage) {
                toastr.success(successMessage);
                sessionStorage.removeItem("successMessage");
            }
        });
    </script>



</body>

</html>
