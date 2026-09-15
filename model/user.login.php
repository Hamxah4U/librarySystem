<?php

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once 'Database.php'; 

$response = [
    'success' => false,
    'message' => '',
    'redirect' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_input     = trim($_POST['userRole'] ?? '');
    $identity_input = trim($_POST['identity'] ?? '');
    $password_input = trim($_POST['password'] ?? '');

    if (empty($identity_input) || empty($password_input) || empty($role_input)) {
        $response['message'] = 'Please fill in all required fields.';
        echo json_encode($response);
        exit;
    }

   
    $sql = "SELECT u.department, u.user_id, u.library_card_no, u.full_name, u.email, u.password_hash, u.status, r.role_name 
        FROM users u
        INNER JOIN roles r ON u.role_id = r.role_id
        WHERE (u.email = :identity OR u.library_card_no = :identity) AND r.role_name = :role
        LIMIT 1
    ";

    $stmt = $db->checkExist($sql, [
        ':identity' => $identity_input,
        ':role'     => $role_input
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password_input, $user['password_hash'])) {
        if ($user['status'] !== 'active') {
            $response['message'] = 'Your account is suspended or inactive. Contact support.';
            echo json_encode($response);
            exit;
        }

        // session variables
        $_SESSION['user_id']         = $user['user_id'];
        $_SESSION['full_name']       = $user['full_name'];
        $_SESSION['role_name']       = $user['role_name'];
        $_SESSION['library_card_no'] = $user['library_card_no'];
        $_SESSION['department']      = $user['department'];

        // redirect route 
        $redirectUrl = '/student-dashboard';
        if ($user['role_name'] === 'librarian' || $user['role_name'] === 'administrator') {
            $redirectUrl = '/staff-dashboard';
        }

        $response['success']  = true;
        $response['message']  = 'Login successful! Redirecting...';
        $response['redirect'] = $redirectUrl;
    } else {
        $response['message'] = 'Invalid email/library ID, password, or selected role.';
    }

    echo json_encode($response);
    exit;
}