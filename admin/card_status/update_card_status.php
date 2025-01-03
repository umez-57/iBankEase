<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountNumber = $_POST['account_number'];
    $isBlocked = $_POST['is_blocked'];

    $stmt = $conn->prepare("UPDATE cards SET is_blocked = ? WHERE account_number = ?");
    $stmt->bind_param("si", $isBlocked, $accountNumber);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update card status']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
