<?php
  $pageTitle = "Staff Dashboard | SmartLib LMS";
  $currentPage = "dashboard";

  require_once 'views/partials/header2.php';
  require_once 'views/partials/sidebar2.php';
?>

<!-- Main Content Area -->
<div class="main-content">
    <?php require_once 'views/partials/topbar2.php'; ?>

    <!-- Flash Notifications -->
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

    <!-- Operational Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card p-3 bg-white">
                <span class="text-muted small fw-bold">TOTAL CATALOG TITLES</span>
                <h2 class="fw-bold my-1 text-primary"><?= number_format($totalBooks) ?></h2>
                <span class="text-muted small"><i class="fa-solid fa-book me-1"></i>Active Titles</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 bg-white">
                <span class="text-muted small fw-bold">BOOKS ON LOAN</span>
                <h2 class="fw-bold my-1 text-info"><?= number_format($booksOnLoan) ?></h2>
                <span class="text-muted small"><i class="fa-solid fa-arrows-rotate me-1"></i>Active Borrowers</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 bg-white">
                <span class="text-muted small fw-bold">OVERDUE ITEMS</span>
                <h2 class="fw-bold my-1 text-danger"><?= number_format($overdueCount) ?></h2>
                <span class="text-danger small"><i class="fa-solid fa-clock me-1"></i>Requires Notice</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 bg-white">
                <span class="text-muted small fw-bold">FINES COLLECTED (MONTH)</span>
                <h2 class="fw-bold my-1 text-success">₦<?= number_format((float)$finesCollected, 2) ?></h2>
                <span class="text-muted small"><i class="fa-solid fa-circle-check me-1"></i>Audit Verified</span>
            </div>
        </div>
    </div>

    <!-- Active Circulation Transactions Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0" style="color: var(--primary-blue);">
              <i class="fa-solid fa-list-check me-2"></i>Active Circulation Logs
            </h5>
            <input type="text" id="logSearchInput" class="form-control form-control-sm w-25" placeholder="Filter student or title...">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="circulationTable">
                    <thead class="table-light">
                        <tr>
                            <th>Trans ID</th>
                            <th>Patron Name</th>
                            <th>Book Title</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Fine</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($circulationLogs)): ?>
                            <?php foreach ($circulationLogs as $log): ?>
                                <tr>
                                    <td><code>#TXN-<?= str_pad($log['loan_id'], 4, '0', STR_PAD_LEFT) ?></code></td>
                                    <td class="fw-bold">
                                        <?= htmlspecialchars($log['full_name']) ?> 
                                        <br><small class="text-muted">(<?= htmlspecialchars($log['library_card_no']) ?>)</small>
                                    </td>
                                    <td><?= htmlspecialchars($log['book_title']) ?></td>
                                    <td><?= date('d M Y', strtotime($log['issue_date'])) ?></td>
                                    <td><?= date('d M Y', strtotime($log['due_date'])) ?></td>
                                    <td>
                                        <?php if ((float)$log['fine_amount'] > 0): ?>
                                            <span class="text-danger fw-bold">₦<?= number_format((float)$log['fine_amount'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">₦0.00</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($log['status'] === 'overdue'): ?>
                                            <span class="badge bg-danger">Overdue</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">Borrowed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No active loan transactions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
require_once 'views/partials/modal_issue_book.php';
require_once 'views/partials/footer2.php'; 
?>

<!-- Client-side filter script -->
<script>
document.getElementById('logSearchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#circulationTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>