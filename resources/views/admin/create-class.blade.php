<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Create new class')</title>
    @include('components.stylelinks')
</head>

<body class="g-sidenav-show bg-gray-100">
    @include('components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

        @section('breadcrumb', 'Class')

        @section('page-title', 'Create New class')
        @include('components.navbar')
        <div class="container-fluid py-4">
            <form id="createClass">
                <div class="form-group">

                    <label for="class">Class name</label>
                    <input type="number" class="form-control" id="name" aria-describedby="class"
                        placeholder="Enter class name" required>
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
            $('#createClass').submit(function(e) {
                e.preventDefault();

                
                let classname = $('#name').val().trim();
                let adminToken = localStorage.getItem("admin_token"); // Retrieve admin token from localStorage

                if (!adminToken) {
                    toastr.error("Unauthorized access. Admin token is missing.");
                    return;
                }

                $.ajax({
                    url: "http://127.0.0.1:8000/api/admin/class/store",
                    type: 'POST',
                    headers: {
                        "Authorization": "Bearer " + adminToken // Attach token in the request headers
                    },
                    data: JSON.stringify({
                        name: classname
                    }),
                    contentType: "application/json",
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#createClass')[0].reset();
                        } else {
                            toastr.error(response.errors);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) { // Unauthorized access
                            toastr.error("Invalid or expired admin token. Please log in again.");
                            localStorage.removeItem("admin_token"); // Remove invalid token
                            setTimeout(function() {
                                window.location.href =
                                "/admin-sign-in"; // Redirect to login page
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
