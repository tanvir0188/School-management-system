<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">@yield('breadcrumb', 'Dashboard')</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">@yield('page-title', 'Dashboard')</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">

            </div>
            <ul class="navbar-nav  justify-content-end">

                <li class="nav-item d-flex align-items-center">
                    Welcome, &nbsp; <span class="studentName text-secondary font-weight-bold"></span>
                </li>


                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="#" class="nav-link text-body p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </li>
                <li class="nav-item px-3 d-flex align-items-center dropdown">
                    <a href="javascript:;" class="nav-link text-body p-0 dropdown-toggle" id="settingsDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                        <li><a class="dropdown-item" href="{{route('student.profile')}}">View Profile</a></li>
                        <li><a class="dropdown-item" href="#" onclick="logoutStudent()">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script>
    function logoutStudent() {
        let token = localStorage.getItem("student_token");

        if (!token) {
            toastr.error("You are not logged in!");
            return;
        }

        fetch("http://127.0.0.1:8000/api/student/logout", {
                method: "POST",
                headers: {
                    "Authorization": "Bearer " + token,
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    localStorage.removeItem("student_token");
                    localStorage.removeItem("student");
                    localStorage.removeItem("student_login_info");
                    toastr.success(data.message);


                    setTimeout(() => {
                        window.location.href = "/student-login";
                    }, 1500);
                } else {
                    toastr.error("Logout failed: " + data.message);
                }
            })
            .catch(error => {
                console.error("Logout Error:", error);
                toastr.error("Something went wrong!");
            });
    }
</script>
