<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard')</title>
    @include('components.stylelinks')
    <style>
        .card:hover {
            background-color:  var(--bs-primary) !important;
            /* Bootstrap primary color */
            transform: translateY(-5px);
            /* Slight lift effect */
        }
    </style>

</head>

<body class="g-sidenav-show bg-gray-100">
    @include('teacher.components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @include('teacher.components.navbar')
        <div class="container-fluid py-4">
            @include('teacher.components.dashboard-cards')



        </div>

    </main>
    @include('components.admin-scripts')
    <script src="{{ asset('assets/js/teacher-ajax-functions/redirect.js') }}"></script>
    <script src="{{ asset('assets/js/teacher-ajax-functions/dashboard.js') }}"></script>



    <script src="{{ asset('assets/js/public-ajax-functions/toastr.js') }}"></script>
</body>

</html>
