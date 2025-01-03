<?php
session_start();
include '../db_connect.php'; // Include your database connection
date_default_timezone_set('Asia/Kolkata'); // Set the timezone

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit;
}

$username = $_SESSION['user'];

// Fetch user's full name from the 'users' table
$sql_user = "SELECT fullname FROM users WHERE username = '$username'";
$result_user = mysqli_query($conn, $sql_user);
$user = mysqli_fetch_assoc($result_user);

// Fetch account details from 'customer_accounts' table
$sql_account = "SELECT * FROM customer_accounts WHERE username = '$username'";
$result_account = mysqli_query($conn, $sql_account);
$account = mysqli_fetch_assoc($result_account);

if (!$account) {
    echo "No account details found for the user.";
    exit;
}

// Extract account details
$fullname        = $user['fullname'];
$account_number  = $account['account_number'];
$branch_address  = $account['branch_address'];
$ifsc_code       = $account['ifsc_code'];
$balance         = $account['balance'];

// Fetch card details including remaining limit, daily limit, and last reset time
$sql_card = "SELECT remaining_limit, daily_limit, last_reset 
             FROM cards 
             WHERE account_number = '$account_number'";
$result_card = mysqli_query($conn, $sql_card);
$card = mysqli_fetch_assoc($result_card);

if ($card) {
    $remaining_limit = $card['remaining_limit'];
    $daily_limit     = $card['daily_limit'];
    $last_reset      = $card['last_reset'];

    // Check if 1 minute has passed since the last reset (for testing/demo)
    $current_time    = new DateTime();
    $last_reset_time = new DateTime($last_reset);
    $interval        = $last_reset_time->diff($current_time);

    if ($interval->i >= 1 || $interval->h > 0 || $interval->d > 0) {
        // Reset remaining limit and update last reset time
        $remaining_limit = $daily_limit;
        $sql_reset_limit = "UPDATE cards 
                            SET remaining_limit = '$daily_limit', last_reset = NOW() 
                            WHERE account_number = '$account_number'";
        mysqli_query($conn, $sql_reset_limit);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Dashboard</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Font Awesome (for icons) -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSsRgjOMZZlC2T9bFVXudxntoHvfZqNn1aI6P48baG9aD7Tr7mv4EX+mkT2UgQ=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />

  <script>
    // Toggle the hidden class on complaint-options
    function toggleComplaintOptions() {
      const complaintOptions = document.getElementById('complaint-options');
      complaintOptions.classList.toggle('hidden');
    }
  </script>
</head>

<body class="bg-gradient-to-r from-blue-200 to-blue-400 min-h-screen flex justify-center items-start pt-10">
  <!-- Main Container: max-w-2xl for extra width -->
  <div class="max-w-2xl w-full mx-auto px-4">
    <!-- Account Info Card: added overflow-x-auto to avoid horizontal clipping -->
    <div class="bg-white rounded-lg shadow-lg p-6 overflow-x-auto">
      <!-- Welcome title -->
      <h1 class="text-3xl font-bold text-gray-700 mb-4 flex items-center gap-2">
        <i class="fas fa-user text-blue-500"></i>
        Welcome, <?php echo htmlspecialchars($fullname); ?>!
      </h1>

      <!-- Account details -->
      <div class="space-y-2 text-gray-700">
        <p><strong>Account Number:</strong> <?php echo htmlspecialchars($account_number); ?></p>
        <p><strong>Branch Address:</strong> <?php echo htmlspecialchars($branch_address); ?></p>
        <p><strong>IFSC Code:</strong> <?php echo htmlspecialchars($ifsc_code); ?></p>
        <p><strong>Available Balance:</strong> 
          ₹<?php echo number_format($balance, 2); ?></p>
        <p><strong>Remaining Daily Limit:</strong> 
          ₹<?php echo number_format($remaining_limit, 2); ?></p>
      </div>

      <!-- Vertical Buttons -->
      <div class="flex flex-col space-y-3 mt-6">
        <!-- Transfer Amount -->
        <button
          onclick="window.location.href='transfer/transfer_form.php';"
          class="flex items-center justify-center gap-2 
                 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded 
                 transition-all transform hover:scale-105"
        >
          <i class="fas fa-money-check-dollar"></i>
          Transfer Amount
        </button>

        <!-- ATM Services -->
        <button
          onclick="window.location.href='atm_services/atm_services.php';"
          class="flex items-center justify-center gap-2 
                 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded 
                 transition-all transform hover:scale-105"
        >
          <i class="fas fa-building"></i>
          ATM Services
        </button>

        <!-- Manage Card -->
        <button
          onclick="window.location.href='card/manage_card.php';"
          class="flex items-center justify-center gap-2 
                 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded 
                 transition-all transform hover:scale-105"
        >
          <i class="fas fa-credit-card"></i>
          Manage Card
        </button>

        <!-- View Transactions -->
        <button
          onclick="window.location.href='trx_history/view_transactions.php';"
          class="flex items-center justify-center gap-2 
                 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded 
                 transition-all transform hover:scale-105"
        >
          <i class="fas fa-list-alt"></i>
          View Transactions
        </button>

        <!-- Raise Complaint -->
        <button
          onclick="toggleComplaintOptions();"
          class="flex items-center justify-center gap-2 
                 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded 
                 transition-all transform hover:scale-105"
        >
          <i class="fas fa-bullhorn"></i>
          Raise Complaint
        </button>

        <!-- Complaint Options, hidden by default. 
             flex-row + flex-nowrap + gap ensures side-by-side. -->
        <div
          id="complaint-options"
          class="hidden flex flex-row flex-nowrap gap-3 mt-2"
        >
          <button
            onclick="window.location.href='file_complaints/complaint_status.php';"
            class="flex-1 flex items-center justify-center gap-2 
                   bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 rounded 
                   transition-all transform hover:scale-105"
          >
            <i class="fas fa-clipboard-check"></i>
            Registered Complaints
          </button>
          <button
            onclick="window.location.href='file_complaints/file_complaint.php';"
            class="flex-1 flex items-center justify-center gap-2 
                   bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 rounded 
                   transition-all transform hover:scale-105"
          >
            <i class="fas fa-pencil-alt"></i>
            File a New Complaint
          </button>
        </div>

        <!-- Logout -->
        <button
          onclick="window.location.href='../logout.php';"
          class="flex items-center justify-center gap-2 
                 bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded 
                 transition-all transform hover:scale-105"
        >
          <i class="fas fa-sign-out-alt"></i>
          Logout
        </button>
      </div>
    </div>
  </div>
</body>
</html>
