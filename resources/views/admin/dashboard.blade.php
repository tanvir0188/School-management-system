<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard')</title>
    @include('components.stylelinks')
    <style>
        /* Add this to your CSS file */
        .hover-effect {
            transition: background-color 0.3s ease;
            /* Smooth transition */
        }

        .card-link:hover .hover-effect {
            background-color: var(--bs-primary) !important;
            /* Change to primary color on hover */
        }
    </style>

</head>

<body class="g-sidenav-show bg-gray-100">
    @include('components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @include('components.navbar')
        <div class="container-fluid py-4">
            @include('components.dashboard-cards')



        </div>

    </main>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/soft-ui-dashboard.min.js?v=1.1.0') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @include('components.admin-auth-redirect')
    <script src="{{ asset('assets/js/ajax-functions/dashboard.js') }}"></script>



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
