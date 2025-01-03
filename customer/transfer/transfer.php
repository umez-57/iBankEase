<?php
session_start();
include 'db_connect.php'; // Database connection
date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ifsc = $_POST['ifsc'];
    $account_number = $_POST['account_number'];
    $amount = $_POST['amount'];
    $sender_username = $_SESSION['user']; // Logged-in user (sender)

    // Fetch recipient account details and full name from users table using a JOIN
    $sql_account = "
        SELECT ca.username, u.fullname, ca.balance, ca.account_number 
        FROM customer_accounts ca 
        JOIN users u ON ca.username = u.username 
        WHERE ca.account_number = '$account_number' 
        AND ca.ifsc_code = '$ifsc'";

    $result_account = mysqli_query($conn, $sql_account);

    if (mysqli_num_rows($result_account) > 0) {
        $recipient = mysqli_fetch_assoc($result_account);
        $recipient_username = $recipient['username'];
        $recipient_name = $recipient['fullname']; // Fetch the full name from the users table
        $recipient_balance = $recipient['balance'];
        $recipient_account = $recipient['account_number'];

        // Get sender's balance, full name, and IFSC
        $sql_sender = "
            SELECT ca.balance, ca.account_number, u.fullname, ca.ifsc_code 
            FROM customer_accounts ca 
            JOIN users u ON ca.username = u.username 
            WHERE ca.username = '$sender_username'";
        $result_sender = mysqli_query($conn, $sql_sender);
        $sender = mysqli_fetch_assoc($result_sender);
        $sender_balance = $sender['balance'];
        $sender_name = $sender['fullname'];
        $sender_account = $sender['account_number'];
        $sender_ifsc = $sender['ifsc_code'];

        // Prevent self-transfer by checking if sender's and recipient's account numbers are the same
        if ($sender_account === $recipient_account) {
            echo "<script>
                    alert('Self-transfer is not allowed.');
                    window.location.href='transfer_form.php';
                  </script>";
            exit();
        }

        // Check if the sender has enough balance
        if ($sender_balance >= $amount) {
            // Deduct amount from sender and add to recipient
            $new_sender_balance = $sender_balance - $amount;
            $new_recipient_balance = $recipient_balance + $amount;

            // Update sender's balance
            $sql_update_sender = "UPDATE customer_accounts SET balance = '$new_sender_balance' WHERE username = '$sender_username'";
            mysqli_query($conn, $sql_update_sender);

            // Update recipient's balance
            $sql_update_recipient = "UPDATE customer_accounts SET balance = '$new_recipient_balance' WHERE account_number = '$account_number'";
            mysqli_query($conn, $sql_update_recipient);

            // Log transaction for sender (Debited)
            $sql_log_sender = "
                INSERT INTO transactions (username, type, amount, balance_after, recipient_account, recipient_name, ifsc_code, date_time)
                VALUES ('$sender_username', 'Debited', '$amount', '$new_sender_balance', '$account_number', '$recipient_name', '$ifsc', NOW())";
            mysqli_query($conn, $sql_log_sender);

            // Log transaction for recipient (Credited)
            $sql_log_recipient = "
                INSERT INTO transactions (username, type, amount, balance_after, recipient_account, recipient_name, ifsc_code, date_time)
                VALUES ('$recipient_username', 'Credited', '$amount', '$new_recipient_balance', '$sender_account', '$sender_name', '$sender_ifsc', NOW())";
            mysqli_query($conn, $sql_log_recipient);

            echo "<script>
                    alert('Transfer successful!');
                    window.location.href='../dashboard.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Insufficient balance!');
                    window.location.href='transfer_form.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Invalid account number or IFSC code!');
                window.location.href='transfer_form.php';
              </script>";
    }
}
?>
