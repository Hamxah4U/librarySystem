<?php
require_once 'model/Database.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$reference = trim($input['reference'] ?? '');
$fineId    = (int)($input['fine_id'] ?? 0);

if (empty($reference) || !$fineId) {
    echo json_encode(['success' => false, 'message' => 'Invalid transaction parameters.']);
    exit();
}

// 1. Verify transaction via Paystack REST API
$paystackSecretKey = "sk_test_YOUR_PAYSTACK_SECRET_KEY"; // Replace with your Paystack Secret Key
$url = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer {$paystackSecretKey}",
    "Cache-Control: no-cache"
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if ($result && isset($result['data']['status']) && $result['data']['status'] === 'success') {
    $paidAmount = $result['data']['amount'] / 100; // Convert Kobo back to Naira

    // 2. Fetch Fine details
    $fine = $db->checkExist("SELECT fine_id, user_id, amount, status FROM fines WHERE fine_id = :fine_id", [':fine_id' => $fineId])->fetch(PDO::FETCH_ASSOC);

    if ($fine && $fine['status'] !== 'paid') {
        // Update fine status
        $db->checkExist(
            "UPDATE fines SET status = 'paid', paid_at = CURRENT_TIMESTAMP() WHERE fine_id = :fine_id", 
            [':fine_id' => $fineId]
        );

        // Record entry in payments table
        $db->checkExist(
            "INSERT INTO payments (user_id, fine_id, reference, amount, payment_method, status) 
             VALUES (:user_id, :fine_id, :reference, :amount, 'paystack', 'success')",
            [
                ':user_id'   => $fine['user_id'],
                ':fine_id'   => $fineId,
                ':reference' => $reference,
                ':amount'    => $paidAmount
            ]
        );

        echo json_encode(['success' => true, 'message' => 'Payment recorded successfully.']);
        exit();
    }
}

echo json_encode(['success' => false, 'message' => 'Payment verification failed or fine already cleared.']);
exit();