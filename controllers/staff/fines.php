<?php
require_once 'model/Database.php';

// Auth Guard: Admin (1) or Librarian (2)
// if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'] ?? 0, [1, 2])) {
//     header('Location: /');
//     exit();
// }

$statusFilter = trim($_GET['status'] ?? 'all');
$searchTerm   = trim($_GET['search'] ?? '');

$whereClause = ["1=1"];
$params      = [];

if (in_array($statusFilter, ['unpaid', 'paid', 'waived'])) {
    $whereClause[] = "f.status = :status";
    $params[':status'] = $statusFilter;
}

if (!empty($searchTerm)) {
    $whereClause[] = "(u.full_name LIKE :search OR u.library_card_no LIKE :search OR b.title LIKE :search OR f.fine_id LIKE :search)";
    $params[':search'] = "%{$searchTerm}%";
}

$whereSql = implode(' AND ', $whereClause);

// Fetch Summary Metrics
$metricsSql = "
    SELECT 
        COALESCE(SUM(CASE WHEN f.status = 'unpaid' THEN f.amount ELSE 0 END), 0.00) AS total_unpaid,
        COALESCE(SUM(CASE WHEN f.status = 'paid' THEN f.amount ELSE 0 END), 0.00) AS total_paid,
        COALESCE(SUM(CASE WHEN f.status = 'waived' THEN f.amount ELSE 0 END), 0.00) AS total_waived,
        (SELECT COUNT(*) FROM loans WHERE status = 'overdue') AS overdue_loans_count
    FROM fines f
";
$metrics = $db->checkExist($metricsSql, [])->fetch(PDO::FETCH_ASSOC);

// Fetch Detailed Fines List
$sql = "
    SELECT 
        f.fine_id,
        f.loan_id,
        f.user_id,
        f.amount,
        f.status AS fine_status,
        f.created_at AS assessed_at,
        f.paid_at,
        u.full_name AS patron_name,
        u.library_card_no,
        u.email AS patron_email,
        b.title AS book_title,
        b.isbn,
        l.due_date,
        l.return_date,
        l.status AS loan_status,
        DATEDIFF(COALESCE(l.return_date, CURRENT_DATE()), l.due_date) AS overdue_days
    FROM fines f
    INNER JOIN users u ON f.user_id = u.user_id
    INNER JOIN loans l ON f.loan_id = l.loan_id
    INNER JOIN books b ON l.book_id = b.book_id
    WHERE {$whereSql}
    ORDER BY f.fine_id DESC
";

$fines = $db->checkExist($sql, $params)->fetchAll(PDO::FETCH_ASSOC) ?: [];

require 'views/staff/fines.view.php';