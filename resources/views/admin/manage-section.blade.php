<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Section: ')</title>
    @include('components.stylelinks')

</head>

<body class="g-sidenav-show bg-gray-100">
    @include('components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @section('breadcrumb', '')
        @section('page-title', '')
        @include('components.navbar')
        <div class="container-fluid py-4">
            <div class="row my-4">
                <div class="col-lg-8 mb-md-2 mb-2">
                    <div class="card">
                        <div id="teacherInfo" class="card-body"></div>
                    </div>
                </div>
                <div class="col-lg-8 mb-md-4 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-lg-6 col-7">
                                    <h6>Students</h6>
                                    <p class="text-sm mb-0">
                                        <i class="fa fa-check text-info" aria-hidden="true"></i>
                                        <span id="studentCount" class="font-weight-bold ms-1"></span> students
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2" id="student-card-body">
                            <div class="table-responsive">
                                <table id="studentTable" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>Photo</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Action</th>
                                        </tr>
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

        @include('admin.section-teacher-modal')




    </main>
    @include('components.admin-scripts')
    <script src="{{ asset('assets/js/ajax-functions/manageSection.js') }}"></script>
    @include('components.admin-auth-redirect')

    <script src="{{ asset('assets/js/public-ajax-functions/toastr.js') }}"></script>




</body>

</html>
