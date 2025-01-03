<?php
session_start();
include '../db_connect.php'; // Include database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    echo "Unauthorized access!";
    exit();
}

$admin_username = $_SESSION['user'];

// Fetch the admin's full name from the database
$sql = "SELECT fullname FROM users WHERE username = '$admin_username' AND role = 'admin'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $admin_name = $row['fullname'];
} else {
    $admin_name = "Admin"; // Fallback if the name is not found
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    /* Optional: Slight scale-up on hover for buttons */
    .animate-hover:hover {
      transform: scale(1.03);
    }
  </style>
</head>

<body class="bg-gradient-to-r from-blue-200 to-blue-400 min-h-screen flex items-start justify-center pt-10">
  <!-- Main Container -->
  <div class="bg-white max-w-md w-full mx-4 rounded-lg shadow-lg p-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-700">
      Welcome to Admin's Portal
    </h1>
    <p class="text-gray-700 mb-6">
      Hello, <span class="font-semibold"><?php echo htmlspecialchars($admin_name); ?></span>
    </p>

    <!-- Dashboard Options -->
    <div class="flex flex-col space-y-3">
      <button
        onclick="location.href='prof_update/admin_user_profile_update.php'"
        class="bg-blue-600 text-white font-semibold py-2 rounded transition transform animate-hover hover:bg-blue-700"
      >
        User Profile Update
      </button>
      <button
        onclick="location.href='acc_update/admin_user_account_status.php'"
        class="bg-blue-600 text-white font-semibold py-2 rounded transition transform animate-hover hover:bg-blue-700"
      >
        User Account Status
      </button>
      <button
        onclick="location.href='card_status/admin_card_status.php'"
        class="bg-blue-600 text-white font-semibold py-2 rounded transition transform animate-hover hover:bg-blue-700"
      >
        User Card Status
      </button>
      <button
        onclick="location.href='balance_update/admin_user_balance.php'"
        class="bg-blue-600 text-white font-semibold py-2 rounded transition transform animate-hover hover:bg-blue-700"
      >
        User Balance Update
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
