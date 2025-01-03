<?php
session_start();
include 'db_connect.php'; // Include your database connection
date_default_timezone_set('Asia/Kolkata');

// Ensure the request is POST and the user is logged in
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
    $action = $_POST['action'];
    $card_number = $_POST['card_number'];

    // Fetch the account number linked to the user
    $sql_get_account = "SELECT account_number FROM customer_accounts WHERE username = '$username'";
    $result_account = mysqli_query($conn, $sql_get_account);

    if (mysqli_num_rows($result_account) > 0) {
        $account = mysqli_fetch_assoc($result_account);
        $account_number = $account['account_number'];

        // Check which action needs to be performed
        if ($action === 'block') {
            // Block the card
            $sql_block = "UPDATE cards SET is_blocked = 'yes' WHERE account_number = '$account_number' AND card_number = '$card_number'";
            if (mysqli_query($conn, $sql_block)) {
                echo json_encode(['status' => 'success', 'message' => 'Card blocked successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Unable to block card.']);
            }
        } elseif ($action === 'unblock') {
            // Unblock the card and reset attempts to 3
            $sql_unblock = "UPDATE cards SET is_blocked = 'no', attempts = 3 WHERE account_number = '$account_number' AND card_number = '$card_number'";
            if (mysqli_query($conn, $sql_unblock)) {
                echo json_encode(['status' => 'success', 'message' => 'Card unblocked successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Unable to unblock card.']);
            }
        } elseif ($action === 'change_limit') {
            // Change the daily limit of the card, update remaining_limit, and log the current time in last_reset_time
            $new_limit = $_POST['new_limit'];
            $current_time = date('Y-m-d H:i:s'); // Get the current time

            $sql_limit = "UPDATE cards SET daily_limit = '$new_limit', remaining_limit = '$new_limit', last_reset = '$current_time' 
                          WHERE account_number = '$account_number' AND card_number = '$card_number'";
            if (mysqli_query($conn, $sql_limit)) {
                echo json_encode(['status' => 'success', 'message' => 'Daily limit and remaining limit updated successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Unable to change daily limit.']);
            }
        } elseif ($action === 'change_pin') {
            // Change the card PIN
            $new_pin = $_POST['new_pin'];
            $sql_pin = "UPDATE cards SET pin = '$new_pin' WHERE account_number = '$account_number' AND card_number = '$card_number'";
            if (mysqli_query($conn, $sql_pin)) {
                echo json_encode(['status' => 'success', 'message' => 'PIN changed successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Unable to change PIN.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Account not found.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
}
?>
