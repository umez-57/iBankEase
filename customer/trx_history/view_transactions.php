<?php
session_start();
include 'db_connect.php'; // Database connection
date_default_timezone_set('Asia/Kolkata');

$username = $_SESSION['user']; // Logged-in user

// Fetch all transactions related to the logged-in user
$sql = "SELECT * FROM transactions WHERE username = '$username' ORDER BY date_time DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Transaction History</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-100 to-blue-200 min-h-screen py-10 px-4">
  <div class="max-w-5xl mx-auto bg-white rounded-lg shadow-lg p-6">
    <h1 class="text-3xl font-bold mb-6 text-gray-700">Your Transaction History</h1>

    <!-- Responsive table container -->
    <div class="overflow-x-auto">
      <table class="w-full table-auto border-collapse">
        <thead>
          <!-- Make the header bold, slightly bigger -->
          <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal font-bold">
            <th class="py-3 px-4 text-left border-b">Transaction ID</th>
            <th class="py-3 px-4 text-left border-b">Type</th>
            <th class="py-3 px-4 text-left border-b">Amount</th>
            <th class="py-3 px-4 text-left border-b">Balance After</th>
            <th class="py-3 px-4 text-left border-b">Recipient</th>
            <th class="py-3 px-4 text-left border-b">IFSC</th>
            <th class="py-3 px-4 text-left border-b">Name</th>
            <th class="py-3 px-4 text-left border-b">Date & Time</th>
          </tr>
        </thead>
        <!-- Body text: bold instead of light -->
        <tbody class="text-gray-600 text-sm font-semibold">
          <?php while ($transaction = mysqli_fetch_assoc($result)) : ?>
            <?php
              // Determine row styling based on transaction type
              $isCredit = in_array($transaction['type'], ['Credited','Deposit']);
              // Class for credited transactions (green background), debited (red background)
              $rowClass = $isCredit 
                ? 'bg-green-50 hover:bg-green-200 text-green-700'
                : 'bg-red-50 hover:bg-red-200 text-red-700';
            ?>
            <tr class="border-b border-gray-200 <?php echo $rowClass; ?>">
              <td class="py-3 px-4 whitespace-nowrap"><?php echo $transaction['transaction_id']; ?></td>
              <td class="py-3 px-4 whitespace-nowrap"><?php echo $transaction['type']; ?></td>
              <td class="py-3 px-4 whitespace-nowrap">
                ₹<?php echo number_format($transaction['amount'], 2); ?>
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                ₹<?php echo number_format($transaction['balance_after'], 2); ?>
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                <?php echo $transaction['recipient_account'] ?: 'N/A'; ?>
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                <?php echo $transaction['ifsc_code'] ?: 'N/A'; ?>
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                <?php echo $transaction['recipient_name'] ?: 'Self'; ?>
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                <?php echo $transaction['date_time']; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
