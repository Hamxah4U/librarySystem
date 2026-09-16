<?php
require_once 'model/Database.php';

// // Auth Guard: Admin (1) or Librarian (2)
// if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'] ?? 0, [1, 2])) {
//     header('Location: /');
//     exit();
// }

// 1. Overall System Metrics
$kpis = $db->checkExist("
    SELECT 
        (SELECT COUNT(*) FROM books) AS total_titles,
        (SELECT COALESCE(SUM(total_copies), 0) FROM books) AS total_inventory_copies,
        (SELECT COUNT(*) FROM loans WHERE status = 'borrowed') AS active_loans,
        (SELECT COUNT(*) FROM loans WHERE status = 'overdue') AS overdue_loans,
        (SELECT COUNT(*) FROM users WHERE role_id = 3) AS registered_students,
        (SELECT COALESCE(SUM(amount), 0.00) FROM fines WHERE status = 'paid') AS total_fines_collected
", [])->fetch(PDO::FETCH_ASSOC);

// 2. Most Borrowed Books (Top 5)
$topBooks = $db->checkExist("
    SELECT b.title, b.isbn, c.category_name, COUNT(l.loan_id) AS borrow_count
    FROM loans l
    INNER JOIN books b ON l.book_id = b.book_id
    INNER JOIN categories c ON b.category_id = c.category_id
    GROUP BY b.book_id
    ORDER BY borrow_count DESC
    LIMIT 5
", [])->fetchAll(PDO::FETCH_ASSOC) ?: [];

// 3. Category Distribution for Charts
$categoryStats = $db->checkExist("
    SELECT c.category_name, COUNT(b.book_id) AS book_count
    FROM categories c
    LEFT JOIN books b ON c.category_id = b.category_id
    GROUP BY c.category_id
    ORDER BY book_count DESC
", [])->fetchAll(PDO::FETCH_ASSOC) ?: [];

// 4. Monthly Circulation Activity (Last 6 Months)
$monthlyLoans = $db->checkExist("
    SELECT 
        DATE_FORMAT(issue_date, '%b %Y') AS month_year,
        COUNT(*) AS loan_count
    FROM loans
    WHERE issue_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
    GROUP BY YEAR(issue_date), MONTH(issue_date)
    ORDER BY issue_date ASC
", [])->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Prepare data for Chart.js
$chartMonths = array_column($monthlyLoans, 'month_year');
$chartLoanCounts = array_column($monthlyLoans, 'loan_count');

$chartCategories = array_column($categoryStats, 'category_name');
$chartCatCounts = array_column($categoryStats, 'book_count');

require 'views/staff/reports.view.php';