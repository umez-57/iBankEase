<?php
session_start();
include 'db_connect.php'; // Database connection

// Disable error reporting to prevent warnings or notices from interfering with JSON
error_reporting(E_ERROR | E_PARSE);
header('Content-Type: application/json');

// Ensure the user is authorized
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'staff') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access!"]);
    exit();
}

$staff_username = $_SESSION['user'];

// Fetch branch address for staff from `users` table
$sql_staff_branch = "
    SELECT branch_address 
    FROM users 
    WHERE username = '$staff_username'
";
$result_staff_branch = mysqli_query($conn, $sql_staff_branch);
$staff = mysqli_fetch_assoc($result_staff_branch);

if (!$staff) {
    echo json_encode(["status" => "error", "message" => "Branch details not found for the staff member."]);
    exit();
}

$branch_address = $staff['branch_address'];

// Ensure that a customer username was provided in the request
if (!isset($_POST['username'])) {
    echo json_encode(["status" => "error", "message" => "No customer selected."]);
    exit();
}

$customer_username = mysqli_real_escape_string($conn, $_POST['username']);

// Fetch complaints for the selected customer
$sql_complaints = "
    SELECT complaint_id, title, status
    FROM complaints
    WHERE username = '$customer_username'
    ORDER BY created_at DESC
";
$result_complaints = mysqli_query($conn, $sql_complaints);

if (!$result_complaints) {
    echo json_encode(["status" => "error", "message" => "Error fetching complaints."]);
    exit();
}

$complaints = [];
while ($row = mysqli_fetch_assoc($result_complaints)) {
    $complaints[] = $row;
}

// Send JSON response with complaints data
echo json_encode([
    "status" => "success",
    "complaints" => $complaints
]);
?>
