<?php
session_start();
include 'db_connect.php'; // Database connection

// Check if the logged-in user is a staff member
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'staff') {
    echo "Unauthorized access!";
    exit();
}

$staff_username = $_SESSION['user'];

// Fetch all actions performed by the logged-in staff member
$sql = "
    SELECT action_id, action_type, target_username, amount, action_details, date_time
    FROM staff_actions
    WHERE staff_username = '$staff_username'
    ORDER BY date_time DESC
";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Staff Action History</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    /* Optional: Hover scale effect */
    .animate-hover:hover {
      transform: scale(1.03);
    }
  </style>
</head>

<body class="bg-gradient-to-r from-blue-200 to-blue-400 min-h-screen flex items-start justify-center pt-10">
  <!-- Main container -->
  <div class="bg-white max-w-5xl w-full mx-4 rounded-lg shadow-lg p-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-700">Staff Action History</h1>
    <p class="text-gray-700 mb-6">
      <strong>Staff Username:</strong> 
      <span class="ml-1"><?php echo htmlspecialchars($staff_username); ?></span>
    </p>

    <!-- Table Container -->
    <div class="overflow-x-auto">
      <table class="w-full table-auto border border-gray-300 border-collapse">
        <!-- Table Head -->
        <thead>
          <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal font-bold">
            <th class="py-3 px-4 border border-gray-300">Action ID</th>
            <th class="py-3 px-4 border border-gray-300">Action Type</th>
            <th class="py-3 px-4 border border-gray-300">Target Username</th>
            <th class="py-3 px-4 border border-gray-300">Amount</th>
            <th class="py-3 px-4 border border-gray-300">Action Details</th>
            <th class="py-3 px-4 border border-gray-300">Date &amp; Time</th>
          </tr>
        </thead>
        <!-- Table Body -->
        <tbody class="text-gray-600 text-sm font-semibold">
          <?php if (mysqli_num_rows($result) > 0) : ?>
            <?php while ($action = mysqli_fetch_assoc($result)) : ?>
              <?php
                // Color-code deposit vs. withdraw
                // "deposit-row" => greenish, "withdraw-row" => red or pinkish
                $actionType = strtolower($action['action_type']);
                if ($actionType === 'deposit') {
                  $rowClass = 'bg-green-50 hover:bg-green-100 text-green-800';
                } elseif ($actionType === 'withdraw') {
                  $rowClass = 'bg-red-50 hover:bg-red-100 text-red-800';
                } else {
                  $rowClass = 'hover:bg-gray-50'; // default
                }
              ?>
              <tr class="border-b border-gray-300 <?php echo $rowClass; ?>">
                <td class="py-3 px-4 border border-gray-300 whitespace-nowrap">
                  <?php echo htmlspecialchars($action['action_id']); ?>
                </td>
                <td class="py-3 px-4 border border-gray-300 whitespace-nowrap">
                  <?php echo htmlspecialchars($action['action_type']); ?>
                </td>
                <td class="py-3 px-4 border border-gray-300 whitespace-nowrap">
                  <?php echo $action['target_username'] ?: 'N/A'; ?>
                </td>
                <td class="py-3 px-4 border border-gray-300 whitespace-nowrap">
                  <?php 
                    echo isset($action['amount']) 
                      ? '₹' . number_format($action['amount'], 2) 
                      : 'N/A'; 
                  ?>
                </td>
                <td class="py-3 px-4 border border-gray-300 whitespace-nowrap">
                  <?php echo $action['action_details'] ?: 'No details'; ?>
                </td>
                <td class="py-3 px-4 border border-gray-300 whitespace-nowrap">
                  <?php echo htmlspecialchars($action['date_time']); ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else : ?>
            <tr>
              <td colspan="6" class="py-3 px-4 text-center text-gray-500 border border-gray-300">
                No actions found for this staff member.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
