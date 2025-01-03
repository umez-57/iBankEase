<?php
include 'db_connect.php'; // Database connection
date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_number = $_POST['account_number'];

    // Check if the account number exists in the customer_accounts table
    $sql = "SELECT username FROM customer_accounts WHERE account_number = '$account_number'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $username = $row['username'];

        // Fetch the full name of the account holder
        $sql_user = "SELECT fullname FROM users WHERE username = '$username'";
        $result_user = mysqli_query($conn, $sql_user);
        $user = mysqli_fetch_assoc($result_user);

        // Return the full name in JSON format
        echo json_encode(['exists' => true, 'name' => $user['fullname']]);
    } else {
        // Account not found
        echo json_encode(['exists' => false]);
    }
}
?>
