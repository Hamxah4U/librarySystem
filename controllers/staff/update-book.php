<?php
require_once 'model/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId          = (int)($_POST['book_id'] ?? 0);
    $categoryId      = (int)($_POST['category_id'] ?? 0);
    $isbn            = trim($_POST['isbn'] ?? '');
    $title           = trim($_POST['title'] ?? '');
    $author          = trim($_POST['author'] ?? '');
    $publisher       = !empty($_POST['publisher']) ? trim($_POST['publisher']) : null;
    $publicationYear = !empty($_POST['publication_year']) ? (int)$_POST['publication_year'] : null;
    $shelfLocation   = trim($_POST['shelf_location'] ?? '');
    $totalCopies     = (int)($_POST['total_copies'] ?? 1);
    $status          = trim($_POST['status'] ?? 'available');

    // 1. Fetch current record
    $currentBook = $db->checkExist("SELECT total_copies, available_copies FROM books WHERE book_id = :id", [':id' => $bookId])->fetch(PDO::FETCH_ASSOC);

    if (!$currentBook) {
        $_SESSION['flash_msg']  = "Book record not found.";
        $_SESSION['flash_type'] = "danger";
    } else {
        // Recalculate available copies based on change in total stock
        $difference      = $totalCopies - $currentBook['total_copies'];
        $availableCopies = max(0, $currentBook['available_copies'] + $difference);

        // Derive status
        if ($status !== 'archived') {
            $status = ($availableCopies > 0) ? 'available' : 'out_of_stock';
        }

        $sql = "UPDATE books SET 
                    category_id = :category_id,
                    isbn = :isbn,
                    title = :title,
                    author = :author,
                    publisher = :publisher,
                    publication_year = :publication_year,
                    shelf_location = :shelf_location,
                    total_copies = :total_copies,
                    available_copies = :available_copies,
                    status = :status
                WHERE book_id = :book_id";

        $db->checkExist($sql, [
            ':category_id'      => $categoryId,
            ':isbn'             => $isbn,
            ':title'            => $title,
            ':author'           => $author,
            ':publisher'        => $publisher,
            ':publication_year' => $publicationYear,
            ':shelf_location'   => $shelfLocation,
            ':total_copies'     => $totalCopies,
            ':available_copies' => $availableCopies,
            ':status'           => $status,
            ':book_id'          => $bookId
        ]);

        $_SESSION['flash_msg']  = "Book '{$title}' updated successfully.";
        $_SESSION['flash_type'] = "success";
    }

    header('Location: /staff-inventory');
    exit();
}