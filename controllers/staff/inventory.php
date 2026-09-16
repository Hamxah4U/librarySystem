<?php
require_once 'model/Database.php';

// Auth Guard: Admin (1) or Librarian (2)
// if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'] ?? 0, [1, 2])) {
//     header('Location: /');
//     exit();
// }

$categoryFilter = (int)($_GET['category_id'] ?? 0);
$statusFilter   = trim($_GET['status'] ?? 'all');
$searchTerm     = trim($_GET['search'] ?? '');

$whereClause = ["1=1"];
$params      = [];

if ($categoryFilter > 0) {
    $whereClause[] = "b.category_id = :cat_id";
    $params[':cat_id'] = $categoryFilter;
}

if (in_array($statusFilter, ['available', 'out_of_stock', 'archived'])) {
    $whereClause[] = "b.status = :status";
    $params[':status'] = $statusFilter;
}

if (!empty($searchTerm)) {
    $whereClause[] = "(b.title LIKE :search OR b.author LIKE :search OR b.isbn LIKE :search OR b.shelf_location LIKE :search)";
    $params[':search'] = "%{$searchTerm}%";
}

$whereSql = implode(' AND ', $whereClause);

// Fetch categories for filter dropdown
$categories = $db->checkExist("SELECT category_id, category_name FROM categories ORDER BY category_name ASC", [])->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Fetch books catalog
$sql = "
    SELECT 
        b.book_id,
        b.category_id,
        b.isbn,
        b.title,
        b.author,
        b.publisher,
        b.publication_year,
        b.shelf_location,
        b.total_copies,
        b.available_copies,
        b.status,
        c.category_name
    FROM books b
    INNER JOIN categories c ON b.category_id = c.category_id
    WHERE {$whereSql}
    ORDER BY b.title ASC
";

$books = $db->checkExist($sql, $params)->fetchAll(PDO::FETCH_ASSOC) ?: [];

require 'views/staff/inventory.view.php';