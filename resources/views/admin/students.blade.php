<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Students')</title>
    @include('components.stylelinks')

</head>

<body class="g-sidenav-show bg-gray-100">
    @include('components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @section('breadcrumb', 'Students')

        @section('page-title', 'Students')
        @include('components.navbar')
        <div class="container-fluid py-4">
            <div class="row my-4">
                <div class="col-lg mb-md-0 mb-4">
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
                                <div class="col text-end">
                                    <div class="col-lg-6 col my-auto text-end">
                                        <div class="input-group">
                                            <form class="d-flex my-2 my-lg-0">
                                                <input id="searchStudents" class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                                                <button id="searchButton" class="btn btn-primary my-2 my-sm-0" type="submit">Search</button>
                                              </form>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table id="studentTable" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Name</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Student Id</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Email</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Class</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Section</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Actions</th>
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
        <div id="paginationLinks" class="mt-3"></div>


    </main>
    @include('components.admin-scripts')
    @include('admin.admin-ajax-functions.get-students')
    @include('components.admin-auth-redirect')




</body>

</html>
