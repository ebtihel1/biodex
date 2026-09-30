<!-- Modal: Edit Campaign -->
<div class="modal fade" id="editCampaignModal" tabindex="-1" aria-labelledby="editCampaignLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #22a065ff, #2e7d32); color: white;">
                <h5 class="modal-title" id="editCampaignLabel">
                    <i class="bi bi-pencil-square"></i> Edit Campaign
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editCampaignForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editCampaignId" name="id">

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Campaign Title</label>
                        <input type="text" name="title" id="editTitle" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea name="description" id="editDescription" rows="4" class="form-control" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editStartDate" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="editStartDate" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="editEndDate" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="editEndDate" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select name="status" id="editStatus" class="form-select">
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
