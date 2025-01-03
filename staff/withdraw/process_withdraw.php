<?php
include 'db_connect.php';
header('Content-Type: application/json');

session_start();
$staff_username = $_SESSION['user']; // Logged-in staff member's username

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $amount = $_POST['amount'];

    // Fetch the user's current balance
    $query = "SELECT balance FROM customer_accounts WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user['balance'] < $amount) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Insufficient balance for withdrawal.'
        ]);
        exit();
    }

    $new_balance = $user['balance'] - $amount;

    // Update the user's balance
    $update_balance_query = "UPDATE customer_accounts SET balance = '$new_balance' WHERE username = '$username'";
    mysqli_query($conn, $update_balance_query);

    // Insert transaction into transaction history for the customer
    $transaction_query = "
        INSERT INTO transactions (username, type, amount, balance_after, recipient_account, recipient_name, ifsc_code, date_time)
        VALUES ('$username', 'Debited', '$amount', '$new_balance', 'STAFF', 'STAFF', (SELECT ifsc_code FROM customer_accounts WHERE username = '$username'), NOW())
    ";
    mysqli_query($conn, $transaction_query);

    // Log the staff action in staff_actions table
    $action_log_query = "
        INSERT INTO staff_actions (staff_username, action_type, target_username, amount, action_details, date_time)
        VALUES ('$staff_username', 'Withdraw', '$username', '$amount', 'Withdrawn by Staff', NOW())
    ";
    mysqli_query($conn, $action_log_query);

    echo json_encode([
        'status' => 'success',
        'message' => 'Amount withdrawn successfully.',
        'new_balance' => $new_balance
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request.'
    ]);
}
?>
s