<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">แก้ไขเอกสาร</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="editSender">
                        <input type="hidden" id="editDistrict">
                        <input type="hidden" id="editBookType">
                        <input type="hidden" id="editBookNumber">
                        <input type="hidden" id="editTitle">
                        <input type="hidden" id="editBudget">
                        <input type="hidden" id="editReceiver">
                        <input type="hidden" id="editParcelData">
                        <input type="hidden" id="editReferenceNumber">
                        <input type="hidden" id="editParcelStatus">
                        <div class="mb-3">
                            <label for="editDocument1" class="form-label">รายละเอียดที่ต้องแก้ไข/เพิ่มเติม ดังนี้:</label>

                            <div>1.</div>
                            <textarea class="form-control" id="editDocument1" rows="1"></textarea>

                            <div>2.</div>
                            <textarea class="form-control" id="editDocument2" rows="1"></textarea>

                            <div>3.</div>
                            <textarea class="form-control" id="editDocument3" rows="1"></textarea>

                            <div>4.</div>
                            <textarea class="form-control" id="editDocument4" rows="1"></textarea>

                            <div>5.</div>
                            <textarea class="form-control" id="editDocument5" rows="1"></textarea>
                        </div>
                        <button type="button" class="btn btn-primary" id="saveEdit">บันทึก</button>
                    </form>
                </div>
            </div>
        </div>
    </div>