<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Create new section')</title>
    @include('components.stylelinks')
</head>

<body class="g-sidenav-show bg-gray-100">
    @include('components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

        @section('breadcrumb', 'Section')
        @section('page-title', 'Create new section')

        @include('components.navbar')

        <div class="container-fluid py-4">
            <form id="createSection">
                <!-- Class Selection -->
                <select class="form-select form-select-lg mb-2" id="className" required>
                    <option selected disabled>Select a class</option>
                </select>

                <!-- Teacher Selection -->
                <select class="form-select form-select-lg mb-2" id="teacher" required>
                    <option selected disabled>Select the teacher</option>
                </select>

                <!-- Section Name -->
                <div class="form-group">
                    <label for="section">Section</label>
                    <input type="text" class="form-control" id="section" placeholder="Enter Section name" required>
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

    

    <script>
        $(document).ready(function() {
            let adminToken = localStorage.getItem("admin_token");

            if (!adminToken) {
                toastr.error("Unauthorized access. Admin token is missing.");
                return;
            }

            // ✅ Fetch Classes and Populate Dropdown
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




            // ✅ Fetch Teachers and Populate Dropdown
            $.ajax({
                url: "http://127.0.0.1:8000/api/admin/teacher-without-pagination/index",
                type: 'GET',
                headers: {
                    "Authorization": "Bearer " + adminToken
                },
                success: function(response) {
                    console.log("API Response:", response); // Debugging

                    // Access the actual teacher data inside the `data` array
                    let teacherList = response.teachers.teachers;

                    if (response.status && Array.isArray(teacherList)) {
                        let teacherDropdown = $("#teacher");
                        teacherDropdown.empty().append(
                            '<option selected disabled>Select the teacher</option>');

                        teacherList.forEach(teacher => {
                            console.log(
                                `Teacher ID: ${teacher.id}, Teacher Name: ${teacher.name}`);
                            teacherDropdown.append(
                                `<option value="${teacher.id}">${teacher.name}</option>`);
                        });
                    } else {
                        console.error("Error: 'teachers.data' is not an array", response.teachers);
                        toastr.error("Unexpected response format.");
                    }
                },
                error: function(xhr) {
                    console.error("AJAX Error:", xhr.responseText);
                    toastr.error("Failed to load teachers.");
                }
            });


            // ✅ Submit Form via AJAX
            $('#createSection').submit(function(e) {
                e.preventDefault();

                let classId = $('#className').val();
                let teacherId = $('#teacher').val();
                let sectionName = $('#section').val().trim();

                if (!classId || !sectionName) {
                    toastr.error("Please fill in all required fields.");
                    return;
                }

                $.ajax({
                    url: "http://127.0.0.1:8000/api/admin/section/store",
                    type: 'POST',
                    headers: {
                        "Authorization": "Bearer " + adminToken
                    },
                    data: JSON.stringify({
                        name: sectionName,
                        class_id: classId,
                        teacher_id: teacherId || null
                    }),
                    contentType: "application/json",
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#createSection')[0].reset();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            toastr.error(
                            "Invalid or expired admin token. Please log in again.");
                            localStorage.removeItem("admin_token");
                            setTimeout(() => window.location.href = "/admin/login", 2000);
                        } else if (xhr.status === 422) {
                            let errors = xhr.responseJSON;

                            if (errors && typeof errors.message === "string") {
                                toastr.error(errors
                                .message); // ✅ Show single validation message
                            } else if (errors && Array.isArray(errors.message)) {
                                errors.message.forEach(err => toastr.error(
                                err)); // ✅ Show multiple validation errors
                            } else {
                                toastr.error(
                                    "Validation failed, but no error messages received.");
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
