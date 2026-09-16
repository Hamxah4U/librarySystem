<?php
require_once 'model/Database.php';

/* // Auth Guard: Admin (1) or Librarian (2)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'] ?? 0, [1, 2])) {
    header('Location: /');
    exit();
}
 */
// Automatically flag overdue loans where due_date < current date
$db->checkExist(
    "UPDATE loans 
     SET status = 'overdue' 
     WHERE status = 'borrowed' AND due_date < CURRENT_DATE()",
    []
);

// Filtering & Search parameters
$statusFilter = trim($_GET['status'] ?? 'all');
$searchTerm   = trim($_GET['search'] ?? '');

$whereClause = ["1=1"];
$params      = [];

if (in_array($statusFilter, ['borrowed', 'overdue', 'returned'])) {
    $whereClause[] = "l.status = :status";
    $params[':status'] = $statusFilter;
}

if (!empty($searchTerm)) {
    $whereClause[] = "(u.full_name LIKE :search OR u.library_card_no LIKE :search OR b.title LIKE :search OR b.isbn LIKE :search)";
    $params[':search'] = "%{$searchTerm}%";
}

$whereSql = implode(' AND ', $whereClause);

// Query circulation logs with patron, book, staff, and fine details
$sql = "
    SELECT 
        l.loan_id,
        l.issue_date,
        l.due_date,
        l.return_date,
        l.status,
        b.book_id,
        b.title AS book_title,
        b.isbn,
        u.full_name AS patron_name,
        u.library_card_no,
        u.email AS patron_email,
        s.full_name AS issued_by_name,
        f.fine_id,
        COALESCE(f.amount, 0.00) AS fine_amount,
        f.status AS fine_status
    FROM loans l
    INNER JOIN books b ON l.book_id = b.book_id
    INNER JOIN users u ON l.user_id = u.user_id
    INNER JOIN users s ON l.issued_by_staff_id = s.user_id
    LEFT JOIN fines f ON l.loan_id = f.loan_id
    WHERE {$whereSql}
    ORDER BY l.loan_id DESC
";

$loans = $db->checkExist($sql, $params)->fetchAll(PDO::FETCH_ASSOC) ?: [];

require_once 'views/staff/circulation.view.php';