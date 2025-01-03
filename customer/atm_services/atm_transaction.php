<?php 
session_start();
include 'db_connect.php';
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "No user is logged in."]);
    exit();
}

$username = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $transaction_type = $_POST['transaction_type'];

    // Fetch the current balance, remaining limit, daily limit, and last reset time
    $sql_balance = "SELECT balance, cards.remaining_limit, cards.daily_limit, cards.last_reset 
                    FROM customer_accounts 
                    JOIN cards ON customer_accounts.account_number = cards.account_number 
                    WHERE customer_accounts.username = '$username'";
    $result_balance = mysqli_query($conn, $sql_balance);

    if ($result_balance && mysqli_num_rows($result_balance) > 0) {
        $row = mysqli_fetch_assoc($result_balance);
        $balance = $row['balance'];
        $remaining_limit = $row['remaining_limit'];
        $daily_limit = $row['daily_limit'];
        $last_reset = $row['last_reset'];

        // Check if 5 minutes have passed since the last reset
        $current_time = new DateTime();
        $last_reset_time = new DateTime($last_reset);
        $interval = $last_reset_time->diff($current_time);

        if ($interval->i >= 5 || $interval->h > 0 || $interval->d > 0) { // Reset if 5 minutes or more has passed
            // Reset remaining limit to daily limit
            $remaining_limit = $daily_limit;

            // Update the remaining limit and last reset time in the database
            $sql_reset_limit = "UPDATE cards 
                                SET remaining_limit = '$daily_limit', last_reset = NOW() 
                                WHERE account_number = (SELECT account_number FROM customer_accounts WHERE username = '$username')";
            mysqli_query($conn, $sql_reset_limit);
        }

        if ($transaction_type == 'Deposit') {
            // Deposit logic: update balance only
            $new_balance = $balance + $amount;
            $sql_update_balance = "UPDATE customer_accounts SET balance = '$new_balance' WHERE username = '$username'";
            mysqli_query($conn, $sql_update_balance);
            logTransaction($username, 'Deposit', $amount, $new_balance);
            echo json_encode(["status" => "success", "message" => "Deposit successful!", "balance" => $new_balance]);

        } elseif ($transaction_type == 'Withdraw') {
            // Withdraw logic: check if withdrawal is within the remaining limit and balance
            if ($amount > $remaining_limit) {
                echo json_encode(["status" => "error", "message" => "Withdrawal amount exceeds remaining daily limit."]);
            } elseif ($amount > $balance) {
                echo json_encode(["status" => "error", "message" => "Insufficient balance for withdrawal."]);
            } else {
                // Update balance and remaining limit
                $new_balance = $balance - $amount;
                $new_remaining_limit = $remaining_limit - $amount;
                
                $sql_update_balance = "UPDATE customer_accounts SET balance = '$new_balance' WHERE username = '$username'";
                mysqli_query($conn, $sql_update_balance);

                $sql_update_limit = "UPDATE cards SET remaining_limit = '$new_remaining_limit' 
                                     WHERE account_number = (SELECT account_number FROM customer_accounts WHERE username = '$username')";
                mysqli_query($conn, $sql_update_limit);

                logTransaction($username, 'Withdraw', $amount, $new_balance);
                echo json_encode(["status" => "success", "message" => "Withdrawal successful!", "balance" => $new_balance, "remaining_limit" => $new_remaining_limit]);
            }
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Unable to fetch balance and limit details."]);
    }
}

function logTransaction($username, $type, $amount, $new_balance) {
    global $conn;
    $date_time = date('Y-m-d H:i:s');
    $sql_log_transaction = "
        INSERT INTO transactions (username, type, amount, balance_after, recipient_account, recipient_name, date_time)
        VALUES ('$username', '$type', '$amount', '$new_balance', 'Self', 'Self', '$date_time')";
    mysqli_query($conn, $sql_log_transaction);
}
