document.addEventListener("DOMContentLoaded", function () {
    // Redirect if token is missing
    let token = localStorage.getItem("student_token");
    if (!token) {
        window.location.href = "http://127.0.0.1:8000/student-login";
        return;
    }

    // Retrieve student data from localStorage
    let student = localStorage.getItem("student");
    let studentInfo = localStorage.getItem("student_login_info");

    // Parse studentInfo safely
    let profile, photoUrl;
    try {
        if (studentInfo) {
            studentInfo = JSON.parse(studentInfo);
            profile = studentInfo?.student_profile;
            photoUrl = profile?.photo ? `/students/${profile.photo}` : `/media/nullPic.webp`;
        }
    } catch (error) {
        console.error("Error parsing student_login_info:", error);
        photoUrl = `/media/nullPic.webp`; // Fallback photo URL
    }

    // Update DOM with student data
    if (student) {
        try {
            let studentData = JSON.parse(student);
            let studentName = studentData.name;
            $(".studentName").text(studentName);
            document.querySelector(".profilePhoto").src = photoUrl;
        } catch (error) {
            console.error("Error parsing student data:", error);
        }
    }
});