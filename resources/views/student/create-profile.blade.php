<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Create Profile')</title>
    @include('components.stylelinks')
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.2.0/mdb.min.css" rel="stylesheet" />

</head>

<body class="g-sidenav-show bg-gray-100">
    @include('student.components.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @section('breadcrumb', 'Create Profile')

        @section('page-title', 'Create Profile')
        @include('student.components.navbar')
        <div class="container-fluid py-4">
            <section class="vh-100" style="background-color: #f4f5f7;">
                <div class="container py-5 h-100">
                    <div class="row d-flex justify-content-center align-items-center h-100">
                        <div class="col col-lg-8">
                            <div class="card shadow-lg p-4" style="border-radius: .5rem;">
                                <h4 class="text-center mb-4">Student Profile Form</h4>
                                <form id="studentProfileForm" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="full_name" class="form-label">Full Name</label>
                                            <input type="text" id="full_name" data-id="" name="full_name" class="form-control" required>
                                        </div>
                                        
                                            <input type="hidden" id="student_id" name="student_id" class="form-control" required>
                                        
                                    </div>
            
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label for="phone_number" class="form-label">Phone Number</label>
                                            <input type="text" id="phone_number" name="phone_number" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="age" class="form-label">Age</label>
                                            <input type="number" id="age" name="age" class="form-control" min="6" required>
                                        </div>
                                    </div>
            
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label for="father_name" class="form-label">Father's Name</label>
                                            <input type="text" id="father_name" name="father_name" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="mother_name" class="form-label">Mother's Name</label>
                                            <input type="text" id="mother_name" name="mother_name" class="form-control">
                                        </div>
                                    </div>
            
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea id="address" name="address" class="form-control" rows="2" required></textarea>
                                        </div>
                                    </div>
            
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <label for="photo" class="form-label">Upload Photo</label>
                                            <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
                                        </div>
                                    </div>
            
                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </main>
    @include('components.admin-scripts')
    <script src="{{ asset('assets/js/student-ajax-functions/redirect.js') }}"></script>
    <script src="{{ asset('assets/js/student-ajax-functions/createProfile.js') }}"></script>
    <script src="{{ asset('assets/js/public-ajax-functions/toastr.js') }}"></script>
</body>

</html>
