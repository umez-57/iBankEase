<?php
session_start();
include 'db_connect.php';
date_default_timezone_set('Asia/Kolkata');

// Fetch logged-in user's balance
$username = $_SESSION['user'];
$sql_balance = "SELECT balance FROM customer_accounts WHERE username = '$username'";
$result_balance = mysqli_query($conn, $sql_balance);
$balance = 0;
if ($result_balance && mysqli_num_rows($result_balance) > 0) {
    $row = mysqli_fetch_assoc($result_balance);
    $balance = $row['balance']; // Fetch the actual balance from the database
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Transfer Amount</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Optional: Your custom JS logic -->
  <script src="transfer.js"></script>

  <style>
    /* Example of an input focus animation (scale + shadow) */
    .animate-focus:focus {
      transform: scale(1.03);
      box-shadow: 0 0 12px rgba(66, 153, 225, 0.4);
    }
  </style>
</head>
<body class="bg-gradient-to-r from-blue-200 to-blue-400 min-h-screen flex items-center justify-center">
  <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
    <h2 class="text-2xl font-bold mb-6 text-gray-700">Transfer Amount</h2>

    <!-- Dynamically display available balance -->
    <p class="text-gray-700 mb-4">
      Available Balance: 
      <span id="available-balance" class="font-semibold">
        ₹<?php echo number_format($balance, 2); ?>
      </span>
    </p>

    <form method="POST" action="transfer.php" class="space-y-5">
      <!-- Account Number Field -->
      <div>
        <label for="account-number" class="block text-gray-700 font-semibold mb-1">
          Recipient's Account Number:
        </label>
        <div class="flex items-center gap-2">
          <input
            type="text"
            id="account-number"
            name="account_number"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 
                   transition-all duration-300 animate-focus"
          />
          <button
            type="button"
            class="bg-blue-600 text-white font-semibold px-4 py-2 rounded transition-all transform hover:scale-105 hover:bg-blue-700"
            onclick="verifyAccount()"
          >
            Verify
          </button>
        </div>
        <p id="account-name" class="text-green-600 mt-1 hidden"></p>
        <p id="account-error" class="text-red-600 mt-1 hidden">Account not found!</p>
      </div>

      <!-- IFSC Code Field -->
      <div>
        <label for="ifsc" class="block text-gray-700 font-semibold mb-1">
          IFSC Code:
        </label>
        <div class="flex items-center gap-2">
          <input
            type="text"
            id="ifsc"
            name="ifsc"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 
                   transition-all duration-300 animate-focus"
          />
          <button
            type="button"
            class="bg-blue-600 text-white font-semibold px-4 py-2 rounded transition-all transform hover:scale-105 hover:bg-blue-700"
            onclick="verifyIFSC()"
          >
            Verify
          </button>
        </div>
        <p id="branch-details" class="text-green-600 mt-1 hidden"></p>
        <p id="ifsc-error" class="text-red-600 mt-1 hidden">Invalid IFSC code!</p>
      </div>

      <!-- Amount Field -->
      <div>
        <label for="amount" class="block text-gray-700 font-semibold mb-1">
          Amount to Transfer:
        </label>
        <input
          type="number"
          id="amount"
          name="amount"
          required
          min="1"
          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 
                 transition-all duration-300 animate-focus"
        />
        <p id="balance-error" class="text-red-600 mt-1 hidden">Insufficient balance!</p>
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        class="w-full bg-green-600 text-white font-semibold py-2 rounded transition-all transform hover:scale-105 hover:bg-green-700"
        onclick="checkAmount()"
      >
        Transfer
      </button>
    </form>
  </div>
</body>
</html>
