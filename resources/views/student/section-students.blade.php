<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Classmates')</title>
    @include('components.stylelinks')

</head>

<body class="g-sidenav-show bg-gray-100">
    @include('student.components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @section('breadcrumb', 'Students')

        @section('page-title', 'Students')
        @include('student.components.navbar')
        <div class="container-fluid py-4">
            <div class="row my-4">
                <div class="col-lg-8 mb-md-0 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-lg-6 col-7">
                                    <h6>students</h6>
                                    <p class="text-sm mb-0">
                                        <i class="fa fa-check text-info" aria-hidden="true"></i>
                                        <span id="studentCount" class="font-weight-bold ms-1"></span> students
                                    </p>
                                </div>
                                
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table id="studentTable" class="table align-items-center mb-0">
                                    <thead>
                                        
                                    </thead>
                                    <tbody id="studentData">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>



        </div>
        <div id="paginationLinks" class="mt-3"></div>


    </main>
    @include('components.admin-scripts')
    <script src="{{ asset('assets/js/student-ajax-functions/redirect.js') }}"></script>
    
    <script src="{{ asset('assets/js/student-ajax-functions/students.js') }}"></script>
    <script src="{{ asset('assets/js/public-ajax-functions/toastr.js') }}"></script>




</body>

</html>
