<div class="modal fade" id="updateExamModal" tabindex="-1" role="dialog" aria-labelledby="updateExamModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateExamModalLabel">Update Exam</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateExamForm">
                    <!-- Class Selection -->
                    <select class="form-select form-select-lg mb-2" id="updateClassName" required>
                        <option selected disabled>Select a class</option>
                    </select>

                    <!-- Exam Type Selection -->
                    <select class="form-select form-select-lg mb-2" id="updateExamType" required>
                        <option selected disabled>Select the exam type</option>
                    </select>

                    <!-- Subject Name -->
                    <div class="form-group">
                        <label for="updateSubject">Subject</label>
                        <input type="text" class="form-control" id="updateSubject" placeholder="Enter subject name" required>
                    </div>

                    <!-- Full Marks -->
                    <div class="form-group">
                        <label for="updateFullMark">Full mark</label>
                        <input type="number" class="form-control" id="updateFullMark" placeholder="Enter full mark" required>
                    </div>

                    <!-- Exam Date -->
                    <div class="form-group">
                        <label for="updateExamDate">Exam Date</label>
                        <div class="col-5">
                            <div class="input-group">
                                <input type="date" class="form-control" id="updateExamDate" placeholder="Select date" required />
                            </div>
                        </div>
                    </div>

                    <!-- Hidden field for exam ID -->
                    <input type="hidden" id="updateExamId" name="exam_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateExamButton">Update</button>
            </div>
        </div>
    </div>
</div>