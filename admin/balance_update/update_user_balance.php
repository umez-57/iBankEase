<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $new_balance = $_POST['new_balance'] ?? '';

    if (empty($username) || empty($new_balance)) {
        echo json_encode(['status' => 'error', 'message' => 'Username and balance are required']);
        exit();
    }

    // Update the balance in customer_accounts
    $stmt = $conn->prepare("UPDATE customer_accounts SET balance = ? WHERE username = ?");
    $stmt->bind_param("ds", $new_balance, $username);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Balance updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update balance']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
