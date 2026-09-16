<?php
$pageTitle   = "Book Inventory | SmartLib LMS";
$currentPage = "inventory";

require_once 'views/partials/header2.php';
require_once 'views/partials/sidebar2.php';
?>

<div class="main-content">
    <?php require_once 'views/partials/topbar2.php'; ?>

    <!-- Flash Notification -->
    <?php if (isset($_SESSION['flash_msg'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show mb-4" role="alert">
            <?= htmlspecialchars($_SESSION['flash_msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
            unset($_SESSION['flash_msg']);
            unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>

    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--primary-blue);">Book Inventory Catalog</h4>
            <p class="text-muted small mb-0">Manage catalog metadata, stock copies, and shelf locations.</p>
        </div>
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addBookModal">
            <i class="fa-solid fa-plus me-2"></i>Add New Book
        </button>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="/staff-inventory" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="0">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>" <?= $categoryFilter === (int)$cat['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="available" <?= $statusFilter === 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="out_of_stock" <?= $statusFilter === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                        <option value="archived" <?= $statusFilter === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search title, author, ISBN, or shelf..." value="<?= htmlspecialchars($searchTerm) ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fa-solid fa-magnifying-glass me-1"></i>Search</button>
                    <a href="/staff-inventory" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Data Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Book Details</th>
                            <th>Category</th>
                            <th>Shelf Location</th>
                            <th>Copies (Avail / Total)</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($books)): ?>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold d-block text-dark"><?= htmlspecialchars($book['title']) ?></span>
                                        <small class="text-muted">By <?= htmlspecialchars($book['author']) ?> | ISBN: <?= htmlspecialchars($book['isbn']) ?></small>
                                    </td>
                                    <td><span class="badge bg-soft-primary text-primary"><?= htmlspecialchars($book['category_name']) ?></span></td>
                                    <td><code><?= htmlspecialchars($book['shelf_location']) ?></code></td>
                                    <td>
                                        <span class="fw-bold <?= $book['available_copies'] > 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= $book['available_copies'] ?>
                                        </span> / <?= $book['total_copies'] ?>
                                    </td>
                                    <td>
                                        <?php if ($book['status'] === 'available'): ?>
                                            <span class="badge bg-success">Available</span>
                                        <?php elseif ($book['status'] === 'out_of_stock'): ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Archived</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill me-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editBookModal<?= $book['book_id'] ?>">
                                            <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Book Modal -->
                                <div class="modal fade" id="editBookModal<?= $book['book_id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content text-start">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Book Record</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="/staff-update-book">
                                                <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                                <div class="modal-body p-4">
                                                    <div class="row">
                                                        <div class="col-md-8 mb-3">
                                                            <label class="form-label fw-bold">Title</label>
                                                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($book['title']) ?>" required>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label fw-bold">ISBN</label>
                                                            <input type="text" name="isbn" class="form-control" value="<?= htmlspecialchars($book['isbn']) ?>" required>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Author</label>
                                                            <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($book['author']) ?>" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Category</label>
                                                            <select name="category_id" class="form-select" required>
                                                                <?php foreach ($categories as $cat): ?>
                                                                    <option value="<?= $cat['category_id'] ?>" <?= $cat['category_id'] == $book['category_id'] ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($cat['category_name']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Publisher</label>
                                                            <input type="text" name="publisher" class="form-control" value="<?= htmlspecialchars($book['publisher'] ?? '') ?>">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Publication Year</label>
                                                            <input type="number" name="publication_year" class="form-control" value="<?= $book['publication_year'] ?? '' ?>">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label fw-bold">Shelf Location</label>
                                                            <input type="text" name="shelf_location" class="form-control" value="<?= htmlspecialchars($book['shelf_location']) ?>" required>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label fw-bold">Total Copies</label>
                                                            <input type="number" name="total_copies" class="form-control" min="1" value="<?= $book['total_copies'] ?>" required>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label fw-bold">Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="available" <?= $book['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                                                                <option value="out_of_stock" <?= $book['status'] === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                                                                <option value="archived" <?= $book['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill"><i class="fa-solid fa-floppy-disk me-1"></i>Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No books found in the inventory matching your criteria.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
//require_once 'views/partials/modals_staff.php';
require_once 'views/partials/footer2.php'; 
?>