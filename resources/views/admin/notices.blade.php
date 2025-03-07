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
    @include('components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @section('breadcrumb', 'Notices')

        @section('page-title', 'Notices')
        @include('components.navbar')
        <div class="container-fluid py-4">
            <div class="row d-flex justify-content-center my-4">
                <div class="col-lg-6 mb-md-0 mb-4">
                    <a class="btn btn-primary" href="{{route('admin.create-notice')}}">Create</a>
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-lg-6 col-7">
                                    <h6>Notices</h6>
                                    <p class="text-sm mb-0">
                                        <i class="fa fa-check text-info" aria-hidden="true"></i>
                                        <span id="noticeCount" class="font-weight-bold ms-1"></span> Notices
                                    </p>
                                </div>

                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table id="noticeTable" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Title</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Date</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="noticeData">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                    </div>
                </div>

            </div>



        </div>
        <div id="paginationLinks" class="mt-3"></div>

        <div id="showNotice" class="col-lg mx-4 mb-md-0 mb-4">
            
        </div>


    </main>
    @include('components.admin-scripts')
    @include('admin.admin-ajax-functions.get-notices')
    @include('components.admin-auth-redirect')




</body>

</html>
