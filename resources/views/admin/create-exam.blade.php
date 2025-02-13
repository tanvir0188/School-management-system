<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Create new result')</title>
    @include('components.stylelinks')
    
    
    

</head>

<body class="g-sidenav-show bg-gray-100">
    @include('components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

        @section('breadcrumb', 'Result')
        @section('page-title', 'Create new exam')

        @include('components.navbar')

        <div class="container-fluid py-4">
            <form id="createExam">
                <!-- Class Selection -->
                <select class="form-select form-select-lg mb-2" id="className" required>
                    <option selected disabled>Select a class</option>
                </select>
                <!-- exam type Selection -->
                <select class="form-select form-select-lg mb-2" id="examType" required>
                    <option selected disabled>Select the exam type</option>
                </select>

                <!-- Subject Name -->
                <div class="form-group">
                    <label for="section">Subject</label>
                    <input type="text" class="form-control" id="subject" placeholder="Enter subject name" required>
                </div>

                <div class="form-group">
                    <label for="section">Full mark</label>
                    <input type="number" class="form-control" id="fullmark" placeholder="Enter full mark" required>
                </div>

                <div class="form-group">
                    <label for="date">Exam Date</label>
                    <div class="col-5">
                        <div class="input-group">
                            <input type="date" class="form-control" id="date" placeholder="Select date" />
                        </div>
                    </div>
                </div>
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
            // ✅ Fetch exam types
            $.ajax({
                url: "http://127.0.0.1:8000/api/exam-type",
                type: 'GET',
                headers: {
                    "Authorization": "Bearer " + adminToken
                },
                success: function(response) {
                    console.log("API Response:", response); // Debugging

                    
                    let examTypeList = response.examTypes.examTypes;

                    if (response.status && Array.isArray(examTypeList)) {
                        let examTypeDropdown = $("#examType");
                        examTypeDropdown.empty().append(
                            '<option selected disabled>Select exam type</option>');

                            examTypeList.forEach(examType => {
                                examTypeDropdown.append(
                                `<option value="${examType.id}">${examType.name}</option>`);
                        });
                    } else {
                        toastr.error("Unexpected response format.");
                    }
                },
                error: function(xhr) {
                    console.error("AJAX Error:", xhr.responseText);
                    toastr.error("Failed to load exam types.");
                }
            });

            // ✅ Submit Form via AJAX
            $('#createExam').submit(function(e) {
                e.preventDefault();

                let className = $('#className').val();
                let examtype = $('#examType').val();
                let subject = $('#subject').val().trim();
                let fullmark = $('#fullmark').val().trim();
                let rawDate =  $('#date').val().trim();
                let examDate = new Date(rawDate).toISOString().split('T')[0];


                if (!className || !examtype || !subject || !fullmark || !examDate) {
                    toastr.error("Please fill in all required fields.");
                    return;
                }

                $.ajax({
                    url: "http://127.0.0.1:8000/api/admin/exam",
                    type: 'POST',
                    headers: {
                        "Authorization": "Bearer " + adminToken
                    },
                    data: JSON.stringify({
                        class_id: className,
                        exam_type_id: examtype,
                        subject: subject,
                        full_marks: fullmark,
                        exam_date: examDate,

                    }),
                    contentType: "application/json",
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#createExam')[0].reset();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            toastr.error("Invalid or expired admin token. Please log in again.");
                            localStorage.removeItem("admin_token");
                            setTimeout(() => window.location.href = "/admin-sign-in", 2000);
                        } else if (xhr.status === 422) {
                            let errors = xhr.responseJSON;

                            if (errors && typeof errors.message === "string") {
                                toastr.error(errors.message); // ✅ Show single validation message
                            } else if (errors && Array.isArray(errors.message)) {
                                errors.message.forEach(err => toastr.error(err)); // ✅ Show multiple validation errors
                            } else {
                                toastr.error("Validation failed, but no error messages received.");
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
