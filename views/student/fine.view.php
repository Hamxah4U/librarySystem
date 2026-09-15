<?php
$pageTitle = "My Fines & Penalties | SmartLib LMS";
$currentPage = "fines";

require_once 'views/partials/header1.php';
require_once 'views/partials/sidebar1.php';
?>

<div class="main-content">
    <?php require_once 'views/partials/topbar1.php'; ?>

    <!-- Flash Message -->
    <?php if (isset($_SESSION['flash_msg'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show mb-4" role="alert">
            <?= htmlspecialchars($_SESSION['flash_msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_msg'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <!-- Outstanding Balance Header -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1 text-muted">Total Outstanding Balance</h5>
                <h2 class="fw-bold mb-0 text-danger">₦<?= number_format((float)$unpaidTotal, 2) ?></h2>
            </div>
            <div>
                <?php if ($unpaidTotal > 0): ?>
                    <span class="badge bg-danger p-2 fs-6"><i class="fa-solid fa-triangle-exclamation me-1"></i> Payment Required</span>
                <?php else: ?>
                    <span class="badge bg-success p-2 fs-6"><i class="fa-solid fa-circle-check me-1"></i> Account Clear</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Fines Table -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0" style="color: var(--primary-blue);">
                <i class="fa-solid fa-receipt me-2"></i>Fines & Penalty Records
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fine ID</th>
                            <th>Book Title</th>
                            <th>Date Issued</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($allFines)): ?>
                            <?php foreach ($allFines as $fine): ?>
                                <tr>
                                    <td><code>#FN-<?= str_pad($fine['fine_id'], 4, '0', STR_PAD_LEFT) ?></code></td>
                                    <td class="fw-bold"><?= htmlspecialchars($fine['book_title'] ?? 'Overdue Fine') ?></td>
                                    <td><?= date('d M Y', strtotime($fine['created_at'])) ?></td>
                                    <td class="fw-bold">₦<?= number_format((float)$fine['amount'], 2) ?></td>
                                    <td>
                                        <?php if ($fine['status'] === 'unpaid'): ?>
                                            <span class="badge bg-danger">Unpaid</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($fine['status'] === 'unpaid'): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-success rounded-pill px-3" 
                                                    onclick="payWithPaystack(<?= $fine['fine_id'] ?>, <?= $fine['amount'] ?>, '<?= htmlspecialchars($_SESSION['email'] ?? '') ?>')">
                                                <i class="fa-solid fa-credit-card me-1"></i> Pay Now
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="fa-solid fa-check text-success"></i> Paid</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No fines record found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Paystack Inline JS -->
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
function payWithPaystack(fineId, amount, userEmail) {
    let handler = PaystackPop.setup({
        key: 'pk_test_1c6a596b3038a16e0667e63a40db884431ffec03', // Replace with your Paystack Public Key
        email: userEmail,
        amount: amount * 100, // Convert NGN to Kobo
        currency: 'NGN',
        ref: 'FINE_' + fineId + '_' + Math.floor((Math.random() * 1000000000) + 1),
        callback: function(response) {
            // Redirect to verify endpoint
            window.location.href = '/student-fines?reference=' + response.reference + '&fine_id=' + fineId;
        },
        onClose: function() {
            alert('Transaction was not completed.');
        }
    });
    handler.openIframe();
}
</script>

<?php require_once 'views/partials/footer1.php'; ?>