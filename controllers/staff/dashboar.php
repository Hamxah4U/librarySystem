<?php 
  require_once 'model/Database.php';

$totalBooks     = $db->checkExist("SELECT COUNT(*) AS total FROM books", [])->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$booksOnLoan    = $db->checkExist("SELECT COUNT(*) AS total FROM loans WHERE status IN ('borrowed', 'overdue')", [])->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$overdueCount   = $db->checkExist("SELECT COUNT(*) AS total FROM loans WHERE status = 'overdue'", [])->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$finesCollected = $db->checkExist("SELECT SUM(amount) AS total FROM fines WHERE status = 'paid' AND MONTH(paid_at) = MONTH(CURRENT_DATE()) AND YEAR(paid_at) = YEAR(CURRENT_DATE())", [])->fetch(PDO::FETCH_ASSOC)['total'] ?? 0.00;

$sqlCirculation = "
    SELECT 
        l.loan_id,
        u.full_name,
        u.library_card_no,
        b.title AS book_title,
        l.issue_date,
        l.due_date,
        l.status,
        COALESCE(f.amount, 0.00) AS fine_amount
    FROM loans l
    INNER JOIN users u ON l.user_id = u.user_id
    INNER JOIN books b ON l.book_id = b.book_id
    LEFT JOIN fines f ON l.loan_id = f.loan_id
    WHERE l.status IN ('borrowed', 'overdue')
    ORDER BY l.created_at DESC
";
$circulationLogs = $db->checkExist($sqlCirculation, [])->fetchAll(PDO::FETCH_ASSOC);

$sqlcategory = "SELECT category_id, category_name FROM categories ORDER BY category_name ASC";
$categories = $db->checkExist($sqlcategory, [])->fetchAll(PDO::FETCH_ASSOC);


require 'views/staff/dashboard.view.php';
?>