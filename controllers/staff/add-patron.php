<?php
require_once 'model/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roleId        = (int)($_POST['role_id'] ?? 3); 
    $libraryCardNo = trim($_POST['library_card_no'] ?? '');
    $fullName      = trim($_POST['full_name'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $department    = !empty($_POST['department']) ? trim($_POST['department']) : null;
    $password      = $_POST['password'] ?? '12345678';

    // Duplicate check
    $existingCard = $db->checkExist("SELECT user_id FROM users WHERE library_card_no = :card", [':card' => $libraryCardNo])->fetch(PDO::FETCH_ASSOC);
    $existingEmail = $db->checkExist("SELECT user_id FROM users WHERE email = :email", [':email' => $email])->fetch(PDO::FETCH_ASSOC);

    if ($existingCard) {
        $_SESSION['flash_msg']  = "Library card number '{$libraryCardNo}' is already registered.";
        $_SESSION['flash_type'] = "danger";
    } elseif ($existingEmail) {
        $_SESSION['flash_msg']  = "Email address '{$email}' is already in use.";
        $_SESSION['flash_type'] = "danger";
    } else {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (role_id, library_card_no, full_name, email, password_hash, department, status) 
                VALUES (:role_id, :library_card_no, :full_name, :email, :password_hash, :department, 'active')";

        $db->checkExist($sql, [
            ':role_id'         => $roleId,
            ':library_card_no' => $libraryCardNo,
            ':full_name'       => $fullName,
            ':email'           => $email,
            ':password_hash'   => $passwordHash,
            ':department'      => $department
        ]);

        $_SESSION['flash_msg']  = "Patron account for '{$fullName}' created successfully.";
        $_SESSION['flash_type'] = "success";
    }

    header('Location: /staff-patrons');
    exit();
}