
document.addEventListener("DOMContentLoaded", function() {
    let token = localStorage.getItem("student_token"); // Use localStorage
    let student = localStorage.getItem("student");
    if(student){
        let studentData = JSON.parse(student);
        let studentName = studentData.name;
        $(".studentName").text(studentName);
    }


    if (!token) {
        window.location.href = "http://127.0.0.1:8000/student-login";
        return;
    }
});
