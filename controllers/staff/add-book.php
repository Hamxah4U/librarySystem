<?php
require_once 'model/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId      = (int)($_POST['category_id'] ?? 0);
    $isbn            = trim($_POST['isbn'] ?? '');
    $title           = trim($_POST['title'] ?? '');
    $author          = trim($_POST['author'] ?? '');
    $publisher       = !empty($_POST['publisher']) ? trim($_POST['publisher']) : null;
    $publicationYear = !empty($_POST['publication_year']) ? (int)$_POST['publication_year'] : null;
    $shelfLocation   = trim($_POST['shelf_location'] ?? '');
    $totalCopies     = (int)($_POST['total_copies'] ?? 1);
    $availableCopies = $totalCopies;
    $status          = ($availableCopies > 0) ? 'available' : 'out_of_stock';

    // 1. Verify Category Foreign Key Exists
    $validCategory = $db->checkExist(
        "SELECT category_id FROM categories WHERE category_id = :cat_id", 
        [':cat_id' => $categoryId]
    )->fetch(PDO::FETCH_ASSOC);

    // 2. Check for Duplicate ISBN
    $existingIsbn = $db->checkExist(
        "SELECT book_id FROM books WHERE isbn = :isbn", 
        [':isbn' => $isbn]
    )->fetch(PDO::FETCH_ASSOC);

    if (!$validCategory) {
        $_SESSION['flash_msg']  = "Please select a valid book category.";
        $_SESSION['flash_type'] = "danger";
    } elseif ($existingIsbn) {
        $_SESSION['flash_msg']  = "A book with ISBN '{$isbn}' already exists.";
        $_SESSION['flash_type'] = "danger";
    } else {
        $sql = "INSERT INTO books (
                    category_id, isbn, title, author, publisher, 
                    publication_year, shelf_location, total_copies, available_copies, status
                ) VALUES (
                    :category_id, :isbn, :title, :author, :publisher, 
                    :publication_year, :shelf_location, :total_copies, :available_copies, :status
                )";

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
            ':status'           => $status
        ]);

        $_SESSION['flash_msg']  = "Book '{$title}' added successfully.";
        $_SESSION['flash_type'] = "success";
    }

    header('Location: /staff-dashboard');
    exit();
}