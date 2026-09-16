<?php
require_once 'model/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loanId = (int)($_POST['loan_id'] ?? 0);

    // Fetch loan & book details
    $loan = $db->checkExist(
        "SELECT l.loan_id, l.book_id, l.user_id, l.due_date, l.status, b.title 
         FROM loans l 
         INNER JOIN books b ON l.book_id = b.book_id 
         WHERE l.loan_id = :loan_id",
        [':loan_id' => $loanId]
    )->fetch(PDO::FETCH_ASSOC);

    if (!$loan) {
        $_SESSION['flash_msg']  = "Loan record not found.";
        $_SESSION['flash_type'] = "danger";
    } elseif ($loan['status'] === 'returned') {
        $_SESSION['flash_msg']  = "This item has already been returned.";
        $_SESSION['flash_type'] = "warning";
    } else {
        $today   = date('Y-m-d');
        $dueDate = $loan['due_date'];

        // 1. Mark loan as returned
        $db->checkExist(
            "UPDATE loans SET status = 'returned', return_date = :return_date WHERE loan_id = :loan_id",
            [':return_date' => $today, ':loan_id' => $loanId]
        );

        // 2. Restock book copy & make available
        $db->checkExist(
            "UPDATE books 
             SET available_copies = available_copies + 1, 
                 status = 'available' 
             WHERE book_id = :book_id",
            [':book_id' => $loan['book_id']]
        );

        // 3. Assess overdue fine if returned past due date (₦100 per overdue day)
        if (strtotime($today) > strtotime($dueDate)) {
            $daysOverdue = (int)ceil((strtotime($today) - strtotime($dueDate)) / 86400);
            $fineRate    = 100.00; // ₦100/day
            $fineAmount  = $daysOverdue * $fineRate;

            // Insert fine if not already billed
            $existingFine = $db->checkExist(
                "SELECT fine_id FROM fines WHERE loan_id = :loan_id", 
                [':loan_id' => $loanId]
            )->fetch(PDO::FETCH_ASSOC);

            if (!$existingFine) {
                $db->checkExist(
                    "INSERT INTO fines (loan_id, user_id, amount, reason, status) 
                     VALUES (:loan_id, :user_id, :amount, :reason, 'unpaid')",
                    [
                        ':loan_id' => $loanId,
                        ':user_id' => $loan['user_id'],
                        ':amount'  => $fineAmount,
                        ':reason'  => "Overdue Fine ({$daysOverdue} days late)"
                    ]
                );
            }
            $_SESSION['flash_msg']  = "Book '{$loan['title']}' returned. Overdue fine of ₦" . number_format($fineAmount, 2) . " generated.";
            $_SESSION['flash_type'] = "warning";
        } else {
            $_SESSION['flash_msg']  = "Book '{$loan['title']}' returned successfully.";
            $_SESSION['flash_type'] = "success";
        }
    }

    header('Location: /staff-circulation');
    exit();
}