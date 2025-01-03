<?php
session_start();
include 'db_connect.php';

// Check if the admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Predefined branch details (address and IFSC code)
$branch_details = [
    'delhi' => ['address' => 'Connaught Place, Delhi', 'ifsc' => 'SBIN0000456'],
    'mumbai' => ['address' => 'Nariman Point, Mumbai', 'ifsc' => 'SBIN0001234'],
    'bangalore' => ['address' => 'MG Road, Bangalore', 'ifsc' => 'SBIN0000789']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Profile Update</title>
    <link rel="stylesheet" href="admin_style.css"> <!-- External CSS for styling -->
</head>
<body>
    <div class="admin-container">
        <h2>Welcome to User Profile Update</h2>
        
        <!-- Branch Selection Dropdown -->
        <label for="branchSelect">Select Branch:</label>
        <select id="branchSelect" onchange="fetchUsersByBranch()">
            <option value="">-- Select Branch --</option>
            <?php foreach ($branch_details as $branch => $details): ?>
                <option value="<?php echo $branch; ?>"><?php echo ucfirst($branch); ?></option>
            <?php endforeach; ?>
        </select>

        <!-- User Table (Populated by JavaScript) -->
        <div id="userTableContainer" style="display: none; margin-top: 20px;">
            <h3>Users in Selected Branch</h3>
            <table id="userTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Fullname</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Branch Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="userTableBody"></tbody>
            </table>
        </div>

        <div id="message" class="message"></div> <!-- Success/Error message container -->
    </div>

    <script src="admin_user_profile.js"></script> <!-- External JavaScript for functionality -->
</body>
</html>
