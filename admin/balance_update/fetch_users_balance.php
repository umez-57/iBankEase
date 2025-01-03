<?php
include 'db_connect.php';

// Predefined branch details
$branch_details = [
    'delhi' => 'Connaught Place, Delhi',
    'mumbai' => 'Nariman Point, Mumbai',
    'bangalore' => 'MG Road, Bangalore'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $branch = $_POST['branch'] ?? '';

    // Check if the branch exists in the branch details
    if (array_key_exists($branch, $branch_details)) {
        // Get the full address for the selected branch
        $branch_address = $branch_details[$branch];

        // Query to get user info along with balance based on the branch address in customer_accounts table
        $sql = "
            SELECT u.username, u.fullname, ca.balance
            FROM users u
            JOIN customer_accounts ca ON u.username = ca.username
            WHERE ca.branch_address = ? 
            AND u.username NOT IN ('admin', 'staff1', 'staff2', 'staff3')
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $branch_address);
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        echo json_encode(['status' => 'success', 'users' => $users]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid branch selected.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>