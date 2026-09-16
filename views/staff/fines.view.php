<?php
$pageTitle   = "Overdue & Fines | SmartLib LMS";
$currentPage = "fines";

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

    <!-- Header -->
    <div class="mb-4">
        <h4 class="fw-bold mb-1" style="color: var(--primary-blue);">Overdue & Fine Management</h4>
        <p class="text-muted small mb-0">Track overdue books, manage assessed penalties, and process fine payments.</p>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-danger border-4">
                <small class="text-muted fw-bold text-uppercase">Outstanding Fines</small>
                <h3 class="fw-bold text-danger mb-0 mt-1">₦<?= number_format((float)($metrics['total_unpaid'] ?? 0), 2) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-success border-4">
                <small class="text-muted fw-bold text-uppercase">Collected Fines</small>
                <h3 class="fw-bold text-success mb-0 mt-1">₦<?= number_format((float)($metrics['total_paid'] ?? 0), 2) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-secondary border-4">
                <small class="text-muted fw-bold text-uppercase">Waived Amount</small>
                <h3 class="fw-bold text-secondary mb-0 mt-1">₦<?= number_format((float)($metrics['total_waived'] ?? 0), 2) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-warning border-4">
                <small class="text-muted fw-bold text-uppercase">Overdue Books</small>
                <h3 class="fw-bold text-warning mb-0 mt-1"><?= (int)($metrics['overdue_loans_count'] ?? 0) ?> Active</h3>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="/staff-fines" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="unpaid" <?= $statusFilter === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                        <option value="paid" <?= $statusFilter === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="waived" <?= $statusFilter === 'waived' ? 'selected' : '' ?>>Waived</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search patron, card no, book title, or fine ID..." value="<?= htmlspecialchars($searchTerm) ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fa-solid fa-magnifying-glass me-1"></i>Search</button>
                    <a href="/staff-fines" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Fines Data Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fine Reference</th>
                            <th>Patron</th>
                            <th>Book / Loan Details</th>
                            <th>Overdue Days</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($fines)): ?>
                            <?php foreach ($fines as $fine): ?>
                                <tr>
                                    <td><code>#FINE-<?= str_pad($fine['fine_id'], 4, '0', STR_PAD_LEFT) ?></code></td>
                                    <td>
                                        <span class="fw-bold d-block text-dark"><?= htmlspecialchars($fine['patron_name']) ?></span>
                                        <small class="text-muted"><?= htmlspecialchars($fine['library_card_no']) ?></small>
                                    </td>
                                    <td>
                                        <span class="fw-bold d-block text-dark"><?= htmlspecialchars($fine['book_title']) ?></span>
                                        <small class="text-muted">Due: <?= date('d M Y', strtotime($fine['due_date'])) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-danger text-danger fw-bold">
                                            <?= max(0, (int)$fine['overdue_days']) ?> Days Overdue
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">₦<?= number_format((float)$fine['amount'], 2) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($fine['fine_status'] === 'paid'): ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php elseif ($fine['fine_status'] === 'waived'): ?>
                                            <span class="badge bg-secondary">Waived</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Unpaid</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($fine['fine_status'] === 'unpaid'): ?>
                                            <form method="POST" action="/staff-update-fine" class="d-inline">
                                                <input type="hidden" name="fine_id" value="<?= $fine['fine_id'] ?>">
                                                <input type="hidden" name="action" value="pay">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill me-1" onclick="return confirm('Record cash payment of ₦<?= number_format((float)$fine['amount'], 2) ?>?')">
                                                    <i class="fa-solid fa-money-bill-wave me-1"></i>Pay Cash
                                                </button>
                                            </form>
                                            <form method="POST" action="/staff-update-fine" class="d-inline">
                                                <input type="hidden" name="fine_id" value="<?= $fine['fine_id'] ?>">
                                                <input type="hidden" name="action" value="waive">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="return confirm('Are you sure you want to waive this fine?')">
                                                    <i class="fa-solid fa-ban me-1"></i>Waive
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <small class="text-muted">
                                                <?= $fine['paid_at'] ? date('d M Y', strtotime($fine['paid_at'])) : 'Resolved' ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No fine records match your filters.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
// require_once 'views/partials/modals_staff.php';
require_once 'views/partials/footer2.php'; 
?>