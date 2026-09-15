<?php

require_once 'model/Database.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}

// Page Metadata
$pageTitle = "My Borrowed Books | SmartLib LMS";
$currentPage = "loans"; // Triggers active link in sidebar

// Fetch full loan history for logged-in user
$user_id = $_SESSION['user_id'];
$sqlLoans = "
    SELECT 
        l.loan_id,
        b.isbn,
        b.title,
        b.author,
        l.issue_date,
        l.due_date,
        l.return_date,
        l.status,
        DATEDIFF(l.due_date, CURDATE()) AS days_left
    FROM loans l
    INNER JOIN books b ON l.book_id = b.book_id
    WHERE l.user_id = :user_id
    ORDER BY l.issue_date DESC
";

$stmtLoans = $db->checkExist($sqlLoans, [':user_id' => $user_id]);
$allLoans = $stmtLoans->fetchAll(PDO::FETCH_ASSOC);

require_once 'views/partials/header1.php';
require_once 'views/partials/sidebar1.php';
?>

<!-- Main Content Area -->
<div class="main-content">
    <?php require_once 'views/partials/topbar1.php'; ?>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0" style="color: var(--primary-blue);">
                <i class="fa-solid fa-book me-2"></i>My Borrowing History & Active Loans
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ISBN</th>
                            <th>Book Title</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($allLoans)): ?>
                            <?php foreach ($allLoans as $loan): ?>
                                <?php 
                                    $issueDate  = date('d M Y', strtotime($loan['issue_date']));
                                    $dueDate    = date('d M Y', strtotime($loan['due_date']));
                                    $returnDate = $loan['return_date'] ? date('d M Y', strtotime($loan['return_date'])) : '—';
                                    
                                    // Status Badge Logic
                                    if ($loan['status'] === 'returned') {
                                        $badge = '<span class="badge bg-secondary">Returned</span>';
                                    } elseif ((int)$loan['days_left'] < 0) {
                                        $badge = '<span class="badge bg-danger">Overdue</span>';
                                    } else {
                                        $badge = '<span class="badge bg-success">Active</span>';
                                    }
                                ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($loan['isbn']) ?></code></td>
                                    <td class="fw-bold"><?= htmlspecialchars($loan['title']) ?></td>
                                    <td><?= $issueDate ?></td>
                                    <td><?= $dueDate ?></td>
                                    <td><?= $returnDate ?></td>
                                    <td><?= $badge ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No borrowing history found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/partials/footer1.php'; ?>