<?php
include 'db_connect.php';
header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['user']; // Logged-in customer's username
    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    // Insert the complaint into the database
    $sql = "INSERT INTO complaints (username, title, category, description) VALUES ('$username', '$title', '$category', '$description')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Complaint filed successfully!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error filing complaint. Please try again later.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request.'
    ]);
}
?>
