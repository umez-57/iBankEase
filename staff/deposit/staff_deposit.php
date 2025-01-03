<?php
session_start();
include 'db_connect.php'; // Database connection

// Check if the logged-in user is a staff member
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'staff') {
    echo "Unauthorized access!";
    exit();
}

$username = $_SESSION['user'];

// Fetch staff branch address
$sql_staff = "SELECT branch_address FROM users WHERE username = '$username'";
$result_staff = mysqli_query($conn, $sql_staff);
$staff = mysqli_fetch_assoc($result_staff);
$branch_address = $staff['branch_address'];

// Fetch all users in the same branch
$sql_users = "
    SELECT u.username, u.fullname, u.email, u.phone, ca.balance
    FROM users u
    JOIN customer_accounts ca ON u.username = ca.username
    WHERE ca.branch_address = '$branch_address'
";
$result_users = mysqli_query($conn, $sql_users);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Staff Deposit Portal</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Your JS file for deposit logic -->
  <script src="staff_deposit.js" defer></script>

  <style>
    /* Optional: Scale + shadow on hover */
    .animate-hover:hover {
      transform: scale(1.03);
    }
  </style>
</head>

<body class="bg-gradient-to-r from-blue-200 to-blue-400 min-h-screen flex items-start justify-center pt-10">
  <!-- Main container with increased width (max-w-5xl) -->
  <div class="bg-white max-w-5xl w-full mx-4 rounded-lg shadow-lg p-6">
    <!-- Title -->
    <h1 class="text-2xl font-bold mb-4 text-gray-700">Staff Deposit Portal</h1>
    <p class="text-gray-700 mb-6">
      <strong>Branch:</strong>
      <span class="ml-1"><?php echo htmlspecialchars($branch_address); ?></span>
    </p>

    <!-- Table Container -->
    <div class="overflow-x-auto">
      <!-- Table with borders -->
      <table class="w-full table-auto border border-gray-300 border-collapse">
        <!-- Table Header -->
        <thead>
          <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal font-bold">
            <th class="py-3 px-4 border border-gray-300">Username</th>
            <th class="py-3 px-4 border border-gray-300">Full Name</th>
            <th class="py-3 px-4 border border-gray-300">Email</th>
            <th class="py-3 px-4 border border-gray-300">Phone</th>
            <th class="py-3 px-4 border border-gray-300">Available Balance</th>
            <th class="py-3 px-4 border border-gray-300">Action</th>
          </tr>
        </thead>
        <!-- Table Body -->
        <tbody class="text-gray-600 text-sm font-semibold">
          <?php while ($user = mysqli_fetch_assoc($result_users)) : ?>
            <tr id="row-<?php echo $user['username']; ?>" class="hover:bg-gray-50">
              <td class="py-3 px-4 border border-gray-300 whitespace-nowrap">
                <?php echo htmlspecialchars($user['username']); ?>
              </td>
              <td class="py-3 px-4 border border-gray-300 whitespace-nowrap">
                <?php echo htmlspecialchars($user['fullname']); ?>
              </td>
              <td class="py-3 px-4 border border-gray-300 whitespace-nowrap">
                <?php echo htmlspecialchars($user['email']); ?>
              </td>
              <td class="py-3 px-4 border border-gray-300 whitespace-nowrap">
                <?php echo htmlspecialchars($user['phone']); ?>
              </td>
              <td class="py-3 px-4 border border-gray-300 whitespace-nowrap balance">
                ₹<?php echo number_format($user['balance'], 2); ?>
              </td>
              <td class="py-3 px-4 border border-gray-300 whitespace-nowrap">
                <!-- Deposit Button -->
                <button
                  onclick="showDepositField('<?php echo $user['username']; ?>')"
                  class="bg-blue-600 text-white font-semibold px-3 py-1 rounded transition transform animate-hover hover:bg-blue-700"
                >
                  Deposit
                </button>
                <!-- Hidden Deposit Form -->
                <div id="deposit-form-<?php echo $user['username']; ?>" class="mt-2 hidden">
                  <input
                    type="number"
                    id="deposit-amount-<?php echo $user['username']; ?>"
                    placeholder="Enter amount"
                    min="1"
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 
                           focus:ring-blue-400 transition-all duration-300 w-36 mr-1"
                  />
                  <button
                    onclick="depositAmount('<?php echo $user['username']; ?>')"
                    class="bg-green-600 text-white font-semibold px-2 py-1 rounded transition transform animate-hover hover:bg-green-700"
                  >
                    Confirm
                  </button>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Message / Notification -->
    <div id="message" class="mt-4 text-center text-sm font-semibold text-red-600 hidden"></div>
  </div>
</body>
</html>
