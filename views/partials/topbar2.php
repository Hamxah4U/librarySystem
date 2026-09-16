<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0" style="color: var(--primary-blue);"><?= $topbarTitle ?? 'Librarian Control Desk' ?></h3>
        <p class="text-muted small"><?= $topbarSubTitle ?? 'Circulation & Inventory Operations Management' ?></p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#issueBookModal">
            <i class="fa-solid fa-plus me-2"></i>Issue New Book
        </button>
        <button class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addBookModal">
            <i class="fa-solid fa-book me-2"></i>Add Title
        </button>
    </div>
</div>