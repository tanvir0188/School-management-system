<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #f1f7fe;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form-box {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .form-box .title {
            font-size: 1.6rem;
            font-weight: bold;
            text-align: center;
        }

        .form-box .subtitle {
            font-size: 1rem;
            text-align: center;
            color: #666;
        }

        .form-container {
            margin-top: 20px;
        }

        .form-container .form-control {
            height: 45px;
            font-size: 1rem;
        }

        .form-box button {
            background-color: #0066ff;
            color: white;
            border-radius: 25px;
            padding: 12px;
            font-size: 1rem;
            font-weight: bold;
            width: 100%;
            transition: 0.3s;
        }

        .form-box button:hover {
            background-color: #005ce6;
        }

        .form-section {
            text-align: center;
            margin-top: 15px;
            font-size: 0.9rem;
        }

        .form-section a {
            font-weight: bold;
            color: #0066ff;
            text-decoration: none;
        }

        .form-section a:hover {
            color: #005ce6;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="form-box">
        <form id="adminLoginForm">
            <span class="title">Admin Login</span>

            <div class="form-container">

                <div class="mb-3">
                    <input type="email" class="form-control" placeholder="Email Address" required id="email">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" placeholder="Password" required id="password">
                </div>
                <button type="submit">Sign in</button>
            </div>
        </form>
        <div class="form-section">
            <p>Don't have an account? Contact <b>01853958635</b> to create one.</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Check if admin_token already exists
            let existingToken = localStorage.getItem("admin_token");

            if (existingToken) {

                sessionStorage.setItem("successMessage", "You are already logged in.");
                window.location.href = "/admin/dashboard";

            } else {

                $("#adminLoginForm").submit(function(e) {
                    e.preventDefault();

                    let email = $("#email").val();
                    let password = $("#password").val();

                    $.ajax({
                        url: "http://127.0.0.1:8000/api/admin/login",
                        type: "POST",
                        data: JSON.stringify({
                            email: email,
                            password: password
                        }),
                        contentType: "application/json",
                        success: function(response) {
                            if (response.status) {
                                localStorage.setItem("admin_token", response
                                .token); // ✅ Save token
                                sessionStorage.setItem("successMessage", response.message);
                                window.location.href = "/admin/dashboard";
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON ? xhr.responseJSON.message :
                                "Login failed";
                            toastr.error(errorMessage);
                        }
                    });
                });
            }
        })
    </script>

</body>

</html>
