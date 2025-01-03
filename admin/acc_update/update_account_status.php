<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $status = $_POST['status'];

    // Prepare SQL statement to update is_blocked status in customer_accounts table
    $stmt = $conn->prepare("UPDATE customer_accounts SET is_blocked = ? WHERE username = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement for customer_accounts']);
        exit();
    }

    $stmt->bind_param("ss", $status, $username);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update account status.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
