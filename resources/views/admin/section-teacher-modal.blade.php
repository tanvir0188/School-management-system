<div class="modal fade" id="changeTeacher" tabindex="-1" role="dialog" aria-labelledby="changeTeacher" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateExamModalLabel">Update teacher</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="changeTeacherForm">
                    <select class="form-select form-select-lg mb-2" id="selectTeacher" required>
                        <option selected disabled>Select teacher</option>
                    </select>
                    <input type="hidden" id="updateTeacherId" name="exam_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateTeacherButton">Update</button>
            </div>
        </div>
    </div>
</div>