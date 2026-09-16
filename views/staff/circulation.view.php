<?php
$pageTitle   = "Circulation Management | SmartLib LMS";
$currentPage = "circulation";

require_once 'views/partials/header2.php';
require_once 'views/partials/sidebar2.php';
?>

<div class="main-content">
    <?php require_once 'views/partials/topbar2.php'; ?>

    <!-- Flash Messages -->
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
            <h4 class="fw-bold mb-1" style="color: var(--primary-blue);">Circulation Register</h4>
            <p class="text-muted small mb-0">Track active loans, process returns, and monitor overdue fines.</p>
        </div>
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#issueBookModal">
            <i class="fa-solid fa-plus me-2"></i>Issue New Book
        </button>
    </div>

    <!-- Filters & Search Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="/staff-circulation" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="borrowed" <?= $statusFilter === 'borrowed' ? 'selected' : '' ?>>Borrowed</option>
                        <option value="overdue" <?= $statusFilter === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                        <option value="returned" <?= $statusFilter === 'returned' ? 'selected' : '' ?>>Returned</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search patron name, card no, ISBN, or book title..." value="<?= htmlspecialchars($searchTerm) ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fa-solid fa-magnifying-glass me-1"></i>Filter</button>
                    <a href="/staff-circulation" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Circulation Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Loan ID</th>
                            <th>Patron</th>
                            <th>Book Details</th>
                            <th>Dates (Issue / Due)</th>
                            <th>Fine</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($loans)): ?>
                            <?php foreach ($loans as $loan): ?>
                                <tr>
                                    <td><code>#TXN-<?= str_pad($loan['loan_id'], 4, '0', STR_PAD_LEFT) ?></code></td>
                                    <td>
                                        <span class="fw-bold d-block"><?= htmlspecialchars($loan['patron_name']) ?></span>
                                        <small class="text-muted"><?= htmlspecialchars($loan['library_card_no']) ?></small>
                                    </td>
                                    <td>
                                        <span class="fw-bold d-block"><?= htmlspecialchars($loan['book_title']) ?></span>
                                        <small class="text-muted">ISBN: <?= htmlspecialchars($loan['isbn']) ?></small>
                                    </td>
                                    <td>
                                        <small class="d-block"><strong>Issued:</strong> <?= date('d M Y', strtotime($loan['issue_date'])) ?></small>
                                        <small class="d-block"><strong>Due:</strong> <?= date('d M Y', strtotime($loan['due_date'])) ?></small>
                                        <?php if ($loan['return_date']): ?>
                                            <small class="text-success d-block"><strong>Returned:</strong> <?= date('d M Y', strtotime($loan['return_date'])) ?></small>
                                        <?php endif; ?>
                                    </td>

                                    <!-- PLACE THE CODE HERE (5th Column) -->
                                    <td>
                                        <?php if ((float)$loan['fine_amount'] > 0): ?>
                                            <span class="text-danger fw-bold">₦<?= number_format((float)$loan['fine_amount'], 2) ?></span>
                                            <br>
                                            <?php if ($loan['fine_status'] === 'paid'): ?>
                                                <span class="badge bg-success">Paid</span>
                                            <?php else: ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger mt-1 rounded-pill"
                                                        onclick="payFine(<?= (int)($loan['fine_id'] ?? 0) ?>, <?= (float)$loan['fine_amount'] ?>, '<?= htmlspecialchars($loan['patron_email'] ?? '') ?>')">
                                                    <i class="fa-solid fa-credit-card me-1"></i>Pay via Paystack
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">₦0.00</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($loan['status'] === 'returned'): ?>
                                            <span class="badge bg-success">Returned</span>
                                        <?php elseif ($loan['status'] === 'overdue'): ?>
                                            <span class="badge bg-danger">Overdue</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">Borrowed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <!-- Mark Returned Form Button -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Add Paystack SDK before footer -->
<script src="https://js.paystack.co/v1/inline.js"></script>

<!-- Inside the Circulation Table Fine Column -->
<td>
    <?php if ((float)$loan['fine_amount'] > 0): ?>
        <span class="text-danger fw-bold">₦<?= number_format((float)$loan['fine_amount'], 2) ?></span>
        <br>
        <?php if ($loan['fine_status'] === 'paid'): ?>
            <span class="badge bg-success">Paid</span>
        <?php else: ?>
            <button type="button" 
                    class="btn btn-xs btn-outline-danger mt-1 rounded-pill"
                    onclick="payFine(<?= (int)$loan['fine_id'] ?>, <?= (float)$loan['fine_amount'] ?>, '<?= htmlspecialchars($loan['patron_email'] ?? 'student@smartlib.edu') ?>')">
                <i class="fa-solid fa-credit-card me-1"></i>Pay via Paystack
            </button>
        <?php endif; ?>
    <?php else: ?>
        <span class="text-muted">₦0.00</span>
    <?php endif; ?>
</td>

<!-- Paystack Popup Handler Script -->
<script>
function payFine(fineId, amountInNaira, studentEmail) {
    let handler = PaystackPop.setup({
        key: 'pk_test_1c6a596b3038a16e0667e63a40db884431ffec03',
        email: studentEmail,
        amount: Math.round(amountInNaira * 100), 
        currency: "NGN",
        ref: 'FINE_' + fineId + '_' + Math.floor((Math.random() * 1000000000) + 1),
        onClose: function() {
            alert('Payment window closed before completion.');
        },
        callback: function(response) {
            // Verify transaction on backend
            fetch('/staff-verify-payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    reference: response.reference,
                    fine_id: fineId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Payment verified and recorded successfully!');
                    location.reload();
                } else {
                    alert('Verification failed: ' + data.message);
                }
            })
            .catch(err => {
                alert('An error occurred while verifying the payment.');
            });
        }
    });
    handler.openIframe();
}
</script>

<?php 
require_once 'views/partials/modal_issue_book.php';
require_once 'views/partials/footer2.php'; 
?>