<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Results')</title>
    @include('components.stylelinks')

</head>

<body class="g-sidenav-show bg-gray-100">
    @include('components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @section('breadcrumb', 'Results')

        @section('page-title', 'Results')
        @include('components.navbar')
        <div class="container-fluid py-4">
            <div class="row my-4">
                <div class="col-lg-8 mb-md-0 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-lg-6 col-7">
                                    <h6>Exams Types</h6>
                                    <p class="text-sm mb-0">
                                        <i class="fa fa-check text-info" aria-hidden="true"></i>
                                        <span id="examTypeCount" class="font-weight-bold ms-1"></span> Exams Types
                                    </p>
                                </div>
                                <div class="col-lg-6 col-5 my-auto text-end">
                                    <a class=" border-radius-md btn btn-primary" href="{{route('admin.results')}}">See the full result list</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table id="examType" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exam Type</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Number of exam</th> 
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th> 
                                        </tr>
                                    </thead>
                                    <tbody id="examTypeData">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        
                    </div>
                </div>

            </div>

            <div class="row my-4 invisible" id="examDetailCard">
                <div class="col-lg-8 mb-md-0 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-lg-6 col-7">
                                    <h6>Exams Details</h6>
                                    <p class="text-sm mb-0">
                                        <i class="fa fa-check text-info" aria-hidden="true"></i>
                                        <span id="examResultCount" class="font-weight-bold ms-1"></span> Exams
                                    </p>
                                </div>
                                <div class="col-lg-6 col-5 my-auto text-end">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table id="examDetails" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exam date</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Class </th> 
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Student Name</th> 
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Student Id</th> 
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Subject</th> 
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Full marks</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Mark</th>
                                        </tr>
                                    </thead>
                                    <tbody id="examDetailData">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        
                    </div>
                </div>
                <div id="paginationLinks" class="mt-3"></div>

            </div>



        </div>


    </main>
    @include('components.admin-scripts')
    @include('admin.admin-ajax-functions.get-results')
    @include('components.admin-auth-redirect')

    


</body>

</html>
