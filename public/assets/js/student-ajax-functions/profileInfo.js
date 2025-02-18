$(document).ready(function () {
    let studentInfo = localStorage.getItem("student_login_info");
    let student = localStorage.getItem("student");

    if (studentInfo && student) {
        studentInfo = JSON.parse(studentInfo);
        student = JSON.parse(student);
        let profile = studentInfo.student_profile;

        if (profile) {
            // Set profile photo
            let photoUrl = profile.photo ? `/students/${profile.photo}` : `/media/nullPic.webp`;
            $("#profilePhoto").attr("src", photoUrl);

            // Set profile information
            $("#student_id").text(student.student_id);
            $("#fullName").text(profile.full_name || "N/A");
            $("#age").text(profile.age || "N/A");
            $("#email").text(student.email || "N/A");
            $("#phone").text(profile.phone_number || "N/A");
            $("#fatherName").text(profile.father_name || "N/A");
            $("#motherName").text(profile.mother_name || "N/A");
            $("#address").text(profile.address || "N/A");
        }
    }
});
