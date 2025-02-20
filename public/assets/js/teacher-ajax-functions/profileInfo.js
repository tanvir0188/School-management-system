$(document).ready(function () {
    try {
        let teacherInfo = localStorage.getItem("teacher_login_info");
        let teacher = localStorage.getItem("teacher");

        if (teacherInfo && teacher) {
            teacherInfo = JSON.parse(teacherInfo);
            teacher = JSON.parse(teacher);

            if (teacherInfo && teacher) {

                let profile = teacherInfo.teacher_profile;


                if (profile) {
                    // Profile exists, hide the "Create Profile" button
                    $("#createProfile").addClass("invisible");
                    $("#profile").addClass("visible");

                    // Set profile photo
                    let photoUrl = profile.photo ? `/teachers/${profile.photo}` : `/media/nullPic.webp`;
                    $("#profilePhoto").attr("src", photoUrl);
                    
                    $("#fullName").text(profile.full_name || "N/A");
                    $("#age").text(profile.age || "N/A");
                    $("#email").text(teacher.email || "N/A");
                    $("#phone").text(profile.phone_number || "N/A");
                    $("#fatherName").text(profile.father_name || "N/A");
                    $("#motherName").text(profile.mother_name || "N/A");
                    $("#address").text(profile.address || "N/A");
                    $("#description").text(profile.description || "N/A");
                } else {
                    // Profile does not exist, show the "Create Profile" button
                    $("#createProfile").addClass("visible")
                    $("#profile").addClass("invisible");
                }
            }
        } else {
            console.error("Teacher data not found in localStorage.");
        }
    } catch (error) {
        console.error("Error parsing teacher data:", error);
    }
});