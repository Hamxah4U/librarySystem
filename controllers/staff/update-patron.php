<?php
require_once 'model/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId        = (int)($_POST['user_id'] ?? 0);
    $roleId        = (int)($_POST['role_id'] ?? 3);
    $libraryCardNo = trim($_POST['library_card_no'] ?? '');
    $fullName      = trim($_POST['full_name'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $department    = !empty($_POST['department']) ? trim($_POST['department']) : null;
    $status        = trim($_POST['status'] ?? 'active');
    $newPassword   = trim($_POST['new_password'] ?? '');

    $patron = $db->checkExist("SELECT user_id FROM users WHERE user_id = :id", [':id' => $userId])->fetch(PDO::FETCH_ASSOC);

    if (!$patron) {
        $_SESSION['flash_msg']  = "Patron account not found.";
        $_SESSION['flash_type'] = "danger";
    } else {
        if (!empty($newPassword)) {
            $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
            $sql = "UPDATE users SET role_id = :role_id, library_card_no = :card, full_name = :name, 
                    email = :email, department = :dept, status = :status, password_hash = :hash WHERE user_id = :id";
            $params = [
                ':role_id' => $roleId, ':card' => $libraryCardNo, ':name' => $fullName,
                ':email'   => $email,  ':dept' => $department,    ':status' => $status,
                ':hash'    => $passwordHash, ':id' => $userId
            ];
        } else {
            $sql = "UPDATE users SET role_id = :role_id, library_card_no = :card, full_name = :name, 
                    email = :email, department = :dept, status = :status WHERE user_id = :id";
            $params = [
                ':role_id' => $roleId, ':card' => $libraryCardNo, ':name' => $fullName,
                ':email'   => $email,  ':dept' => $department,    ':status' => $status,
                ':id'      => $userId
            ];
        }

        $db->checkExist($sql, $params);

        $_SESSION['flash_msg']  = "Patron '{$fullName}' updated successfully.";
        $_SESSION['flash_type'] = "success";
    }

    header('Location: /staff-patrons');
    exit();
}