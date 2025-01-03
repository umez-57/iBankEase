<?php
include 'db_connect.php'; // Include your database connection
date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Plain text password
    $branch = $_POST['branch']; // Branch selected by the user
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Predefined branch details (address and IFSC code)
    $branch_details = [
        'delhi' => ['address' => 'Connaught Place, Delhi', 'ifsc' => 'SBIN0000456'],
        'mumbai' => ['address' => 'Nariman Point, Mumbai', 'ifsc' => 'SBIN0001234'],
        'bangalore' => ['address' => 'MG Road, Bangalore', 'ifsc' => 'SBIN0000789']
    ];

    // Get the selected branch address and IFSC
    $branch_address = $branch_details[$branch]['address'];
    $ifsc_code = $branch_details[$branch]['ifsc'];

    // Generate unique 12-digit account number
    function generateUniqueAccountNumber($conn) {
        do {
            $account_number = str_pad(rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
            $sql_check = "SELECT * FROM customer_accounts WHERE account_number = '$account_number'";
            $result_check = mysqli_query($conn, $sql_check);
        } while (mysqli_num_rows($result_check) > 0);
        
        return $account_number;
    }

    $account_number = generateUniqueAccountNumber($conn);
    $balance = 1000.00; // Initial balance for new accounts

    // Insert into 'users' table (for user authentication)
    $sql_user = "INSERT INTO users (fullname, username, password, role, email, phone) 
                 VALUES ('$fullname', '$username', '$password', 'customer', '$email', '$phone')";
    
    if (mysqli_query($conn, $sql_user)) {
        // Get the new user's ID
        $user_id = mysqli_insert_id($conn);

        // Insert into 'customer_accounts' table (for account details)
        $sql_account = "INSERT INTO customer_accounts (username, account_number, branch_address, ifsc_code, balance)
                        VALUES ('$username', '$account_number', '$branch_address', '$ifsc_code', '$balance')";
        
        if (mysqli_query($conn, $sql_account)) {
            // Automatically create a card for the user
            $card_number = '4076' . str_pad(rand(0, 999999999999), 12, '0', STR_PAD_LEFT); // Generate 16-digit card number starting with '4076'
            $expiry_date = '2029-05-31'; // Set common expiry date for all cards
            $cvv = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT); // Generate random 3-digit CVV
            $pin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT); // Generate random 4-digit PIN
            $daily_limit = 10000; // Default daily limit

            $sql_card = "INSERT INTO cards (card_number, account_number, customer_id, expiry_date, card_type, daily_limit, is_blocked, pin, cvv)
                         VALUES ('$card_number', '$account_number', '$user_id', '$expiry_date', 'debit', '$daily_limit', 'no', '$pin', '$cvv')";

            if (mysqli_query($conn, $sql_card)) {
                // Success message with redirect to login page
                echo "<script>
                        alert('Registration and card creation successful! Now redirecting to login page...');
                        window.location.href='index.html';
                      </script>";
            } else {
                echo "Error creating card: " . mysqli_error($conn);
            }
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
