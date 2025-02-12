<script>
    $.ajax({
        url: `http://127.0.0.1:8000/api/studentCount`,
        type: "GET",
        dataType: "json",
        success: function(response) {
            let studentCount = response.studentCount;
            let student = $('#studentCount')
            student.text(studentCount);
            console.log(studentCount);

        },
    });
    $.ajax({
        url: `http://127.0.0.1:8000/api/teacherCount`,
        type: "GET",
        dataType: "json",
        success: function(response) {
            let teacherCount = response.teacherCount;
            let teacher = $('#teacherCount')
            teacher.text(teacherCount);
            console.log(teacherCount);

        },
    });

    $.ajax({
        url: `http://127.0.0.1:8000/api/classCount`,
        type: "GET",
        dataType: "json",
        success: function(response) {
            let classCount = response.classCount;
            let classC = $('#classCount')
            classC.text(classCount);
            console.log(classCount);

        },
    });

    $.ajax({
        url: `http://127.0.0.1:8000/api/sectionCount`,
        type: "GET",
        dataType: "json",
        success: function(response) {
            let sectionCount = response.sectionCount;
            let section = $('#sectionCount')
            section.text(sectionCount);
            console.log(sectionCount);
        },
    });

    
</script>
