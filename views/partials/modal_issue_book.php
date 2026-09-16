<!-- Issue New Book Modal -->
<div class="modal fade" id="issueBookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-arrow-right-arrow-left me-2"></i>Issue Book to Student
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="/staff-issue-book">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold">Patron / Student Card No.</label>
            <input type="text" name="library_card_no" class="form-control" placeholder="e.g., STU/2026/0142" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Book ISBN or ID</label>
            <input type="text" name="isbn_or_id" class="form-control" placeholder="e.g., 978-0131103627" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Issue Date</label>
              <input type="date" name="issue_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Due Date</label>
              <input type="date" name="due_date" class="form-control"
                value="<?= date('Y-m-d', strtotime('+14 days')) ?>" required>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill"><i class="fa-solid fa-check me-1"></i>Confirm
            Loan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add New Title Modal -->
<div class="modal fade" id="addBookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-book me-2"></i>Add New Catalog Title</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="/staff-add-book">
        <div class="modal-body p-4">
          <div class="row">
            <div class="col-md-8 mb-3">
              <label class="form-label fw-bold">Book Title</label>
              <input type="text" name="title" class="form-control" placeholder="e.g., Clean Architecture" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-bold">ISBN</label>
              <input type="text" name="isbn" class="form-control" placeholder="978-0134494166" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Author</label>
              <input type="text" name="author" class="form-control" placeholder="Robert C. Martin" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
              <select name="category_id" class="form-select" required>
                
                <option value="" selected disabled>-- Select Category --</option>
                <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['category_id'] ?>">
                  <?= htmlspecialchars($cat['category_name']) ?>
                </option>
                <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Total Copies</label>
              <input type="number" name="total_copies" class="form-control" min="1" value="1" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Shelf Location</label>
              <input type="text" name="shelf_location" class="form-control" placeholder="Shelf B-04">
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill"><i class="fa-solid fa-plus me-1"></i>Save
            Title</button>
        </div>
      </form>
    </div>
  </div>
</div>