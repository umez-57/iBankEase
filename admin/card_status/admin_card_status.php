<?php
session_start();
include 'db_connect.php';

// Check if the admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Card Status</title>
    <link rel="stylesheet" href="admin_style.css"> <!-- External CSS for styling -->
</head>
<body>
    <div class="admin-container">
        <h2>Welcome to User Card Status</h2>
        
        <!-- Branch Selection Dropdown -->
        <label for="branchSelect">Select Branch:</label>
        <select id="branchSelect" onchange="fetchUsersByBranch()">
            <option value="">-- Select Branch --</option>
            <option value="delhi">Delhi</option>
            <option value="mumbai">Mumbai</option>
            <option value="bangalore">Bangalore</option>
        </select>

        <!-- User Table (Populated by JavaScript) -->
        <div id="userTableContainer" style="display: none; margin-top: 20px;">
            <h3>Users in Selected Branch</h3>
            <table id="userTable">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Balance</th>
                        <th>Card Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="userTableBody"></tbody>
            </table>
        </div>

        <div id="message" class="message"></div> <!-- Success/Error message container -->
    </div>

    <script src="admin_card_status.js"></script> <!-- External JavaScript for functionality -->
</body>
</html>
