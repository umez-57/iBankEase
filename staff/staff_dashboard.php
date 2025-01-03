<?php
session_start();
include 'db_connect.php'; // Database connection

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'staff') {
    echo "Unauthorized access!";
    exit();
}

$username = $_SESSION['user'];

// Fetch staff member details
$sql_staff = "SELECT fullname, branch_address FROM users WHERE username = '$username'";
$result_staff = mysqli_query($conn, $sql_staff);

if (mysqli_num_rows($result_staff) > 0) {
    $staff = mysqli_fetch_assoc($result_staff);
    $fullname = $staff['fullname'];
    $branch_address = $staff['branch_address'];
} else {
    echo "Error fetching staff details.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Staff Dashboard</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    /* Extra animations (optional) */
    .animate-hover:hover {
      transform: scale(1.03);
    }
  </style>
</head>

<!-- Body with a gradient background, slightly top-aligned -->
<body class="bg-gradient-to-r from-blue-200 to-blue-400 min-h-screen flex items-start justify-center pt-10">

  <!-- Card container -->
  <div class="bg-white max-w-md w-full mx-4 rounded-lg shadow-lg p-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-700">Welcome to Staff's Portal</h1>
    <p class="text-gray-700 mb-1">
      <strong>Name:</strong> 
      <span class="ml-1"><?php echo htmlspecialchars($fullname); ?></span>
    </p>
    <p class="text-gray-700 mb-4">
      <strong>Branch:</strong>
      <span class="ml-1"><?php echo htmlspecialchars($branch_address); ?></span>
    </p>

    <!-- Dashboard Options -->
    <div class="flex flex-col space-y-3">
      <button
        onclick="window.location.href='deposit/staff_deposit.php';"
        class="bg-blue-600 text-white font-semibold py-2 rounded transition transform animate-hover hover:bg-blue-700"
      >
        Deposit Cash
      </button>
      <button
        onclick="window.location.href='withdraw/staff_withdraw.php';"
        class="bg-blue-600 text-white font-semibold py-2 rounded transition transform animate-hover hover:bg-blue-700"
      >
        Withdraw Cash
      </button>
      <button
        onclick="window.location.href='actions/staff_actions.php';"
        class="bg-blue-600 text-white font-semibold py-2 rounded transition transform animate-hover hover:bg-blue-700"
      >
        Staff Actions
      </button>
      <button
        onclick="window.location.href='complaints/view_complaints.php';"
        class="bg-blue-600 text-white font-semibold py-2 rounded transition transform animate-hover hover:bg-blue-700"
      >
        View Complaints
      </button>
      <button
        onclick="window.location.href='../logout.php';"
        class="bg-red-500 text-white font-semibold py-2 rounded transition transform animate-hover hover:bg-red-600"
      >
        Logout
      </button>
    </div>
  </div>
</body>
</html>
