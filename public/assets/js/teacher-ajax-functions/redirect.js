document.addEventListener("DOMContentLoaded", function () {
    // Redirect if token is missing
    let token = localStorage.getItem("teacher_token");
    if (!token) {
        window.location.href = "http://127.0.0.1:8000/teacher-login";
        return;
    }

    // Retrieve teacher data from localStorage
    let teacher = localStorage.getItem("teacher");
    let teacherInfo = localStorage.getItem("teacher_login_info");

    // Parse teacherInfo safely
    let profile, photoUrl;
    try {
        if (teacherInfo) {
            teacherInfo = JSON.parse(teacherInfo);
            profile = teacherInfo?.teacher_profile;
            photoUrl = profile?.photo ? `/teachers/${profile.photo}` : `/media/nullPic.webp`;
        }
    } catch (error) {
        console.error("Error parsing teacher_login_info:", error);
        photoUrl = `/media/nullPic.webp`; // Fallback photo URL
    }

    // Update DOM with teacher data
    if (teacher) {
        try {
            let teacherData = JSON.parse(teacher);
            let teacherName = teacherData.name;
            $(".teacherName").text(teacherName);
            document.querySelector(".profilePhoto").src = photoUrl;
        } catch (error) {
            console.error("Error parsing teacher data:", error);
        }
    }
});