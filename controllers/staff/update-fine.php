<?php
require_once 'model/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fineId = (int)($_POST['fine_id'] ?? 0);
    $action = trim($_POST['action'] ?? ''); // 'pay' or 'waive'

    $fine = $db->checkExist("SELECT fine_id, amount, status FROM fines WHERE fine_id = :id", [':id' => $fineId])->fetch(PDO::FETCH_ASSOC);

    if (!$fine) {
        $_SESSION['flash_msg']  = "Fine record not found.";
        $_SESSION['flash_type'] = "danger";
    } elseif ($fine['status'] !== 'unpaid') {
        $_SESSION['flash_msg']  = "Fine is already marked as " . htmlspecialchars($fine['status']) . ".";
        $_SESSION['flash_type'] = "warning";
    } else {
        if ($action === 'pay') {
            $sql = "UPDATE fines SET status = 'paid', paid_at = CURRENT_TIMESTAMP WHERE fine_id = :id";
            $db->checkExist($sql, [':id' => $fineId]);
            $_SESSION['flash_msg']  = "Fine #{$fineId} recorded as paid successfully.";
            $_SESSION['flash_type'] = "success";
        } elseif ($action === 'waive') {
            $sql = "UPDATE fines SET status = 'waived', paid_at = CURRENT_TIMESTAMP WHERE fine_id = :id";
            $db->checkExist($sql, [':id' => $fineId]);
            $_SESSION['flash_msg']  = "Fine #{$fineId} was waived successfully.";
            $_SESSION['flash_type'] = "info";
        }
    }

    header('Location: /staff-fines');
    exit();
}