<?php
require_once 'model/Database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle Paystack Payment Callback
if (isset($_GET['reference']) && isset($_GET['fine_id'])) {
    $fine_id   = (int)$_GET['fine_id'];
    $reference = trim($_GET['reference']);

    // Fetch fine details to verify ownership and amount
    $stmtFine = $db->checkExist(
        "SELECT amount, status FROM fines WHERE fine_id = :fine_id AND user_id = :user_id",
        [':fine_id' => $fine_id, ':user_id' => $user_id]
    );
    $fineRecord = $stmtFine->fetch(PDO::FETCH_ASSOC);

    if ($fineRecord && $fineRecord['status'] === 'unpaid') {
        // Prevent duplicate payment entry
        $stmtCheckRef = $db->checkExist(
            "SELECT payment_id FROM payments WHERE reference = :reference",
            [':reference' => $reference]
        );

        if ($stmtCheckRef->rowCount() === 0) {
            // 1. Insert payment details into `payments` table
            $db->checkExist(
                "INSERT INTO payments (user_id, fine_id, reference, amount, payment_method, status) 
                 VALUES (:user_id, :fine_id, :reference, :amount, 'paystack', 'success')",
                [
                    ':user_id'   => $user_id,
                    ':fine_id'   => $fine_id,
                    ':reference' => $reference,
                    ':amount'    => $fineRecord['amount']
                ]
            );

            // 2. Update status in `fines` table
            $db->checkExist(
                "UPDATE fines SET status = 'paid', paid_at = NOW() WHERE fine_id = :fine_id AND user_id = :user_id",
                [':fine_id' => $fine_id, ':user_id' => $user_id]
            );

            $_SESSION['flash_msg']  = "Payment successfully recorded! Fine #FN-" . str_pad($fine_id, 4, '0', STR_PAD_LEFT) . " is now cleared.";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_msg']  = "This payment reference has already been processed.";
            $_SESSION['flash_type'] = "warning";
        }
    }

    header('Location: /student-fines');
    exit();
}

// Fetch Total Unpaid Balance
$stmtTotal = $db->checkExist(
    "SELECT SUM(amount) AS total FROM fines WHERE user_id = :user_id AND status = 'unpaid'",
    [':user_id' => $user_id]
);
$unpaidTotal = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0.00;

// Fetch All Fine Records
$sqlFines = "
    SELECT 
        f.fine_id,
        f.amount,
        f.status,
        f.created_at,
        b.title AS book_title
    FROM fines f
    LEFT JOIN loans l ON f.loan_id = l.loan_id
    LEFT JOIN books b ON l.book_id = b.book_id
    WHERE f.user_id = :user_id
    ORDER BY f.created_at DESC
";
$stmtFines = $db->checkExist($sqlFines, [':user_id' => $user_id]);
$allFines  = $stmtFines->fetchAll(PDO::FETCH_ASSOC);

require 'views/student/fine.view.php';