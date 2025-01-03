<?php
session_start();
include 'db_connect.php'; // Database connection

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'staff') {
    echo "Unauthorized access!";
    exit();
}

$staff_username = $_SESSION['user'];

// Fetch the branch address of the staff from `users` table
$sql_staff_branch = "
    SELECT branch_address 
    FROM users 
    WHERE username = '$staff_username'
";
$result_staff_branch = mysqli_query($conn, $sql_staff_branch);
$staff = mysqli_fetch_assoc($result_staff_branch);

if (!$staff) {
    echo "Branch details not found for the staff member.";
    exit();
}

$branch_address = $staff['branch_address'];

// Fetch all customers in the same branch as the staff, using `customer_accounts` for customers
$sql_customers = "
    SELECT u.username, u.fullname 
    FROM users u
    JOIN customer_accounts ca ON u.username = ca.username
    WHERE ca.branch_address = '$branch_address' AND u.role = 'customer'
";
$result_customers = mysqli_query($conn, $sql_customers);
$customers = mysqli_fetch_all($result_customers, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>View Complaints</title>
  
  <!-- Keep your existing CSS -->
  <link rel="stylesheet" href="comp1.css">
  
  <!-- Minimal custom style for background & dropdown -->
  <style>
    /* Background gradient for the entire page */
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #dceefc, #ffffff);
    }

    /* Slight styling for the dropdown */
    #customerSelect {
      padding: 6px;
      border-radius: 4px;
      border: 1px solid #ccc;
      font-size: 14px;
      outline: none;
    }
  </style>

  <!-- Existing JS for loading complaints -->
  <script src="view_complaints.js" defer></script>
</head>

<body>
  <div class="dashboard-container">
    <h1>View Complaints</h1>

    <!-- Customer Selection Dropdown -->
    <div>
      <label for="customerSelect">Select Customer:</label>
      <select id="customerSelect" onchange="loadComplaints(this.value)">
        <option value="">-- Select Customer --</option>
        <?php foreach ($customers as $customer): ?>
          <option value="<?php echo $customer['username']; ?>">
            <?php echo $customer['fullname']; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Complaint Table, initially hidden -->
    <div id="complaintTable" style="display:none; margin-top: 20px;">
      <h3>Complaints</h3>
      <table>
        <thead>
          <tr>
            <th>Complaint ID</th>
            <th>Title</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="complaintBody"></tbody>
      </table>

      <div id="message"></div>
      <div id="complaintDetails" style="display:none; margin-top: 20px;"></div>
    </div>
  </div>
</body>
</html>
