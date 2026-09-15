<?php

require_once 'model/Database.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle Hold Cancellation Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_hold_id'])) {
    $hold_id = (int)$_POST['cancel_hold_id'];
    $db->checkExist(
        "UPDATE holds SET status = 'cancelled' WHERE hold_id = :hold_id AND user_id = :user_id AND status = 'pending'",
        [':hold_id' => $hold_id, ':user_id' => $user_id]
    );
    $_SESSION['flash_msg'] = "Hold request cancelled successfully.";
    header('Location: student-holds.php');
    exit;
}

// Page Metadata
$pageTitle = "My Hold Requests | SmartLib LMS";
$currentPage = "holds"; // Sets active sidebar link

// Fetch all hold requests for logged-in user
$sqlHolds = "
    SELECT 
        h.hold_id,
        b.isbn,
        b.title,
        b.author,
        b.shelf_location,
        b.available_copies,
        h.request_date,
        h.status
    FROM holds h
    INNER JOIN books b ON h.book_id = b.book_id
    WHERE h.user_id = :user_id
    ORDER BY h.request_date DESC
";

$stmtHolds = $db->checkExist($sqlHolds, [':user_id' => $user_id]);
$allHolds = $stmtHolds->fetchAll(PDO::FETCH_ASSOC);

require_once 'views/partials/header1.php';
require_once 'views/partials/sidebar1.php';
?>

<!-- Main Content Area -->
<div class="main-content">
    <?php require_once 'views/partials/topbar1.php'; ?>

    <?php if (isset($_SESSION['flash_msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <?= htmlspecialchars($_SESSION['flash_msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_msg']); ?>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0" style="color: var(--primary-blue);">
                <i class="fa-solid fa-clock-rotate-left me-2"></i>My Book Hold Requests
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ISBN</th>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>Request Date</th>
                            <th>Shelf Location</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($allHolds)): ?>
                            <?php foreach ($allHolds as $hold): ?>
                                <?php 
                                    $reqDate = date('d M Y, h:i A', strtotime($hold['request_date']));
                                    
                                    // Dynamic Badge Rendering
                                    switch ($hold['status']) {
                                        case 'pending':
                                            $badge = '<span class="badge bg-info text-dark">Pending Pickup</span>';
                                            break;
                                        case 'fulfilled':
                                            $badge = '<span class="badge bg-success">Fulfilled</span>';
                                            break;
                                        case 'cancelled':
                                            $badge = '<span class="badge bg-secondary">Cancelled</span>';
                                            break;
                                        case 'expired':
                                            $badge = '<span class="badge bg-danger">Expired</span>';
                                            break;
                                        default:
                                            $badge = '<span class="badge bg-light text-dark">' . htmlspecialchars($hold['status']) . '</span>';
                                    }
                                ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($hold['isbn']) ?></code></td>
                                    <td class="fw-bold"><?= htmlspecialchars($hold['title']) ?></td>
                                    <td><?= htmlspecialchars($hold['author']) ?></td>
                                    <td><?= $reqDate ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($hold['shelf_location']) ?></span></td>
                                    <td><?= $badge ?></td>
                                    <td>
                                        <?php if ($hold['status'] === 'pending'): ?>
                                            <form method="POST" action="student-holds.php" onsubmit="return confirm('Are you sure you want to cancel this hold request?');" style="display:inline;">
                                                <input type="hidden" name="cancel_hold_id" value="<?= $hold['hold_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                                    Cancel Request
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No hold requests found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/partials/footer1.php'; ?>