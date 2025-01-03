<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Prepare query to fetch user from users table
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Compare the entered password
        if ($password === $user['password']) {
            // Check if the user is blocked by referencing the customer_accounts table
            $stmt = $conn->prepare("SELECT is_blocked FROM customer_accounts WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result_blocked = $stmt->get_result();

            if ($result_blocked->num_rows > 0) {
                $account = $result_blocked->fetch_assoc();

                if ($account['is_blocked'] === 'yes') {
                    echo json_encode(["status" => "error", "message" => "Account is blocked due to security reasons. Please contact your nearest branch."]);
                    exit();
                }
            }

            // Proceed with setting session variables if not blocked
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($role === 'staff') {
                echo json_encode(["status" => "success", "redirect" => "staff/staff_dashboard.php", "message" => "Login successful! Redirecting to staff dashboard..."]);
            } elseif ($role === 'customer') {
                echo json_encode(["status" => "success", "redirect" => "customer/dashboard.php", "message" => "Login successful! Redirecting to customer dashboard..."]);
            } else {
                echo json_encode(["status" => "success", "redirect" => "admin/admin_dashboard.php", "message" => "Login successful! Redirecting to admin dashboard..."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Incorrect password!"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found with the specified role!"]);
    }
}
