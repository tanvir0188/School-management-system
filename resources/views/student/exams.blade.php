<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Exams')</title>
    @include('components.stylelinks')
</head>

<body class="g-sidenav-show bg-gray-100">
    @include('student.components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @section('breadcrumb', 'Exams')
        @section('page-title', 'Exams')
        @include('student.components.navbar')
        <div id="resultContainer" class="container-fluid py-4">
            
        </div>
    </main>
    @include('components.admin-scripts')
    <script src="{{ asset('assets/js/student-ajax-functions/redirect.js') }}"></script>
    <script src="{{ asset('assets/js/student-ajax-functions/exams.js') }}"></script>
    <script src="{{ asset('assets/js/public-ajax-functions/toastr.js') }}"></script>
</body>

</html>
