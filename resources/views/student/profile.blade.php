<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Profile')</title>
    @include('components.stylelinks')
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.2.0/mdb.min.css" rel="stylesheet" />

</head>

<body class="g-sidenav-show bg-gray-100">
    @include('student.components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @section('breadcrumb', 'Profile')

        @section('page-title', 'Profile')
        @include('student.components.navbar')
        <div class="container-fluid py-4">
            <section class="vh-100" style="background-color: #f4f5f7;">
                
                <div class="row d-flex justify-content-center h-100">
                    <div class="col col-lg-6 mb-4 mb-lg-0">
                        <div class="card mb-3" style="border-radius: .5rem;">
                            <div class="row g-0">
                                <div class="col-md-4 gradient-custom text-center text-muted"
                                    style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
                                    <img id="profilePhoto" src="{{asset('media/nullPic.webp')}}" alt="Avatar" class="img-fluid my-5" style="width: 80px;" />
                                    <h5 id="fullName"></h5>
                                    <p><b>Student ID:&nbsp;</b><span id="student_id"></span></p>
                                    <p><b>Age:&nbsp;</b><span id="age"></span></p>
                                    <a href="{{route('student.create-profile')}}"><i title="Edit profile" class="far fa-edit mb-5"></i></a>
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body p-4">
                                        <h6>Informations    </h6>
                                        <hr class="mt-0 mb-4">
                                        <div class="row pt-1">
                                            <div class="col-6 mb-3">
                                                <h6>Email</h6>
                                                <p id="email" class="text-muted"></p>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <h6>Phone</h6>
                                                <p id="phone" class="text-muted"></p>
                                            </div>
                                        </div>
                                        <div class="row pt-1">
                                            <div class="col-6 mb-3">
                                                <h6>Father's Name</h6>
                                                <p id="fatherName" class="text-muted"></p>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <h6>Mother's Name</h6>
                                                <p id="motherName" class="text-muted"></p>
                                            </div>
                                        </div>
                                        <div class="row pt-1">
                                            <div class="col-6 mb-3">
                                                <h6>Address</h6>
                                                <p id="address" class="text-muted"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
            </section>




        </div>

    </main>
    @include('components.admin-scripts')
    <script src="{{ asset('assets/js/student-ajax-functions/redirect.js') }}"></script>

    <script src="{{ asset('assets/js/student-ajax-functions/profileInfo.js') }}"></script>
    <script src="{{ asset('assets/js/public-ajax-functions/toastr.js') }}"></script>
</body>

</html>
