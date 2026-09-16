<?php
require_once 'model/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cardNo    = trim($_POST['library_card_no'] ?? '');
    $isbnOrId  = trim($_POST['isbn_or_id'] ?? '');
    $issueDate = $_POST['issue_date'] ?? date('Y-m-d');
    $dueDate   = $_POST['due_date'] ?? date('Y-m-d', strtotime('+14 days'));
    $staffId   = $_SESSION['user_id'] ?? null;

    // 1. Verify active staff user exists in database
    $staffUser = $db->checkExist(
        "SELECT user_id FROM users WHERE user_id = :staff_id", 
        [':staff_id' => $staffId]
    )->fetch(PDO::FETCH_ASSOC);

    // 2. Locate student/patron
    $user = $db->checkExist(
        "SELECT user_id FROM users WHERE library_card_no = :card_no", 
        [':card_no' => $cardNo]
    )->fetch(PDO::FETCH_ASSOC);

    // 3. Locate book
    $book = $db->checkExist(
        "SELECT book_id, available_copies FROM books WHERE isbn = :identifier OR book_id = :identifier", 
        [':identifier' => $isbnOrId]
    )->fetch(PDO::FETCH_ASSOC);

    if (!$staffUser) {
        $_SESSION['flash_msg']  = "Invalid or expired staff session. Please re-login.";
        $_SESSION['flash_type'] = "danger";
    } elseif (!$user) {
        $_SESSION['flash_msg']  = "Student library card number not found.";
        $_SESSION['flash_type'] = "danger";
    } elseif (!$book) {
        $_SESSION['flash_msg']  = "Book title or ISBN not found.";
        $_SESSION['flash_type'] = "danger";
    } elseif ($book['available_copies'] < 1) {
        $_SESSION['flash_msg']  = "No available copies remaining for this title.";
        $_SESSION['flash_type'] = "warning";
    } else {
        // 4. Insert loan record with required staff ID
        $sqlInsertLoan = "INSERT INTO loans (
                            book_id, user_id, issued_by_staff_id, issue_date, due_date, status
                          ) VALUES (
                            :book_id, :user_id, :staff_id, :issue_date, :due_date, 'borrowed'
                          )";

        $db->checkExist($sqlInsertLoan, [
            ':book_id'    => $book['book_id'],
            ':user_id'    => $user['user_id'],
            ':staff_id'   => $staffUser['user_id'],
            ':issue_date' => $issueDate,
            ':due_date'   => $dueDate
        ]);

        // 5. Update book stock count and status
        $db->checkExist(
            "UPDATE books SET 
                available_copies = available_copies - 1, 
                status = IF(available_copies - 1 <= 0, 'out_of_stock', 'available') 
             WHERE book_id = :book_id", 
            [':book_id' => $book['book_id']]
        );

        $_SESSION['flash_msg']  = "Book loan issued successfully.";
        $_SESSION['flash_type'] = "success";
    }

    header('Location: /staff-dashboard');
    exit();
}