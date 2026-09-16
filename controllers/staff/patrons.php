<?php
require_once 'model/Database.php';

// Auth Guard: Admin (1) or Librarian (2)
// if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'] ?? 0, [1, 2])) {
//     header('Location: /');
//     exit();
// }

$roleFilter   = (int)($_GET['role_id'] ?? 0);
$statusFilter = trim($_GET['status'] ?? 'all');
$searchTerm   = trim($_GET['search'] ?? '');

$whereClause = ["1=1"];
$params      = [];

if ($roleFilter > 0) {
    $whereClause[] = "u.role_id = :role_id";
    $params[':role_id'] = $roleFilter;
}

if (in_array($statusFilter, ['active', 'suspended', 'inactive'])) {
    $whereClause[] = "u.status = :status";
    $params[':status'] = $statusFilter;
}

if (!empty($searchTerm)) {
    $whereClause[] = "(u.full_name LIKE :search OR u.email LIKE :search OR u.library_card_no LIKE :search OR u.department LIKE :search)";
    $params[':search'] = "%{$searchTerm}%";
}

$whereSql = implode(' AND ', $whereClause);

// Fetch roles for filter dropdown & form select
$roles = $db->checkExist("SELECT role_id, role_name FROM roles ORDER BY role_id ASC", [])->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Fetch patrons list with aggregated active loan and unpaid fine counts
$sql = "
    SELECT 
        u.user_id,
        u.role_id,
        u.library_card_no,
        u.full_name,
        u.email,
        u.department,
        u.status,
        u.created_at,
        r.role_name,
        (SELECT COUNT(*) FROM loans l WHERE l.user_id = u.user_id AND l.status IN ('borrowed', 'overdue')) AS active_loans,
        (SELECT COALESCE(SUM(f.amount), 0.00) FROM fines f WHERE f.user_id = u.user_id AND f.status = 'unpaid') AS unpaid_fines
    FROM users u
    INNER JOIN roles r ON u.role_id = r.role_id
    WHERE {$whereSql}
    ORDER BY u.user_id DESC
";

$patrons = $db->checkExist($sql, $params)->fetchAll(PDO::FETCH_ASSOC) ?: [];

require 'views/staff/patrons.view.php';