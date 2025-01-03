<?php
include 'db_connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id = $_POST['complaint_id'] ?? '';

    // Check if complaint ID is provided
    if (empty($complaint_id)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Complaint ID is missing'
        ]);
        exit;
    }

    // Fetch complaint details from the database
    $sql = "SELECT title, category, description, status, created_at FROM complaints WHERE complaint_id = '$complaint_id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $complaint = mysqli_fetch_assoc($result);
        
        echo json_encode([
            'status' => 'success',
            'complaint' => $complaint
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Complaint not found'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>
