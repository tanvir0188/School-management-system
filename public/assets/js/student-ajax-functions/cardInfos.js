
$.ajax({
    url: `http://127.0.0.1:8000/api/studentCount`,
    type: "GET",
    dataType: "json",
    success: function (response) {
        let studentCount = response.studentCount;
        let student = $('#studentCount')
        student.text(studentCount);
        console.log(studentCount);

    },
});
$(document).ready(function () {
    let studentLoginInfo = localStorage.getItem('student_login_info')
     
     if (studentLoginInfo) {
        let studentInfo = JSON.parse(studentLoginInfo);
        $('#className').text(studentInfo.class_name);
        $('#sectionName').text(studentInfo.section_name);
        if(studentInfo.teacher_name === "N/A"){
            $('#classTeacher').text('No Teacher Assigned');
        }else{
            $('#classTeacher').text(studentInfo.teacher_name);
        }
        
     }
     
});
