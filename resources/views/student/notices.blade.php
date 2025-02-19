<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Notices')</title>
    @include('components.stylelinks')

</head>

<body class="g-sidenav-show bg-gray-100">
    @include('student.components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @section('breadcrumb', 'Notices')

        @section('page-title', 'Notices')
        @include('student.components.navbar')
        <div class="container-fluid py-4">
            <div class="row my-4">
                <div class="col-lg-6 mb-md-4 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-lg col-7">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6>School Announcements</h6>
                                            <p class="text-sm mb-0">
                                                <i class="fa fa-check text-info" aria-hidden="true"></i>
                                                <span id="schoolNoticeCount" class="font-weight-bold ms-1"></span> Notices
                                            </p>
                                        </div>
                                        
                                        <div id="schoolNoticePaginationLinks" class=""></div>
                                    </div>
                                    
                                </div>
                                

                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table id="schoolNoticeTable" class="table align-items-center mb-0">
                                    <tbody id="schoolNoticeData">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-lg-6 mb-md-0 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6>Section Announcements</h6>
                                        <p class="text-sm mb-0">
                                            <i class="fa fa-check text-info" aria-hidden="true"></i>
                                            <span id="sectionNoticeCount" class="font-weight-bold ms-1"></span> Notices
                                        </p>
                                    </div>
                                    
                                    <div id="sectionNoticePaginationLinks" class=""></div>
                                </div>

                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table id="sectionNoticeTable" class="table align-items-center mb-0">
                                    <tbody id="sectionNoticeData">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>



        </div>

        <div id="showNotice" class="col-lg-8 mx-4 mb-md-0 mb-4">

        </div>


    </main>
    @include('components.admin-scripts')
    <script src="{{ asset('assets/js/student-ajax-functions/redirect.js') }}"></script>
    <script src="{{ asset('assets/js/student-ajax-functions/notices.js') }}"></script>
    <script src="{{ asset('assets/js/student-ajax-functions/sectionNotices.js') }}"></script>

    <script src="{{ asset('assets/js/public-ajax-functions/toastr.js') }}"></script>




</body>

</html>
