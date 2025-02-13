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
                <div class="col-lg mb-md-0 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-lg-6 col-7">
                                    <h6>Results</h6>
                                    <p class="text-sm mb-0">
                                        <i class="fa fa-check text-info" aria-hidden="true"></i>
                                        <span id="resultCount" class="font-weight-bold ms-1"></span> Results
                                    </p>
                                </div>
                                <div class="col my-auto text-end">
                                    <div class="input-group">
                                        <form class="d-flex my-2 my-lg-0">
                                            <input id="searchResults" class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                                            <button id="searchButton" class="btn btn-primary my-2 my-sm-0" type="submit">Search</button>
                                          </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table id="result" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exam Type</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Subject</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Class</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Section</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Student</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Student Id</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Full mark</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Mark</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th> 
                                        </tr>
                                    </thead>
                                    <tbody id="resultData">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        
                    </div>
                </div>

            </div>
            <div id="paginationLinks" class="mt-3"></div>
        </div>


    </main>
    @include('components.admin-scripts')
    <script src="{{ asset('assets/js/ajax-functions/get-result.js') }}"></script>
    @include('components.admin-auth-redirect')

    


</body>

</html>
