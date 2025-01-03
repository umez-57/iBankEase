<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $branch = $_POST['branch'];

    // Update `users` table
    $stmt = $conn->prepare("UPDATE users SET fullname=?, password=?, email=?, phone=?, branch_address=? WHERE id=?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement for users table']);
        exit();
    }
    $stmt->bind_param("sssssi", $fullname, $password, $email, $phone, $branch, $id);
    $stmt->execute();

    if ($stmt->error) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update user in users table: ' . $stmt->error]);
        exit();
    }
    $stmt->close();

    // Update `branch_address` in `customer_accounts` table using the user `id`
    $branch_details = [
        'delhi' => 'Connaught Place, Delhi',
        'mumbai' => 'Nariman Point, Mumbai',
        'bangalore' => 'MG Road, Bangalore'
    ];
    $branch_address = $branch_details[$branch];

    $stmt = $conn->prepare("UPDATE customer_accounts SET branch_address=? WHERE username=(SELECT username FROM users WHERE id=?)");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement for customer_accounts']);
        exit();
    }
    $stmt->bind_param("si", $branch_address, $id);
    $stmt->execute();

    if ($stmt->error) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update branch address in customer_accounts: ' . $stmt->error]);
        exit();
    }
    $stmt->close();

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
