<?php
session_start();
include 'db_connect.php';
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION['user'])) {
    echo "Error: No user is logged in.";
    exit();
}

$username = $_SESSION['user'];

$sql_card = "
    SELECT balance, cards.is_blocked, cards.pin, cards.attempts, cards.card_number, 
           cards.daily_limit, cards.remaining_limit
    FROM customer_accounts 
    JOIN cards ON customer_accounts.account_number = cards.account_number 
    WHERE customer_accounts.username = '$username'
";
$result_card = mysqli_query($conn, $sql_card);

$balance         = 0.00;
$is_blocked      = 'no';
$pin             = '';
$attempts        = 3;
$card_number     = '';
$daily_limit     = 0;
$remaining_limit = 0;

if ($result_card && mysqli_num_rows($result_card) > 0) {
    $row             = mysqli_fetch_assoc($result_card);
    $balance         = $row['balance'];
    $is_blocked      = $row['is_blocked'];
    $pin             = $row['pin'];
    $attempts        = $row['attempts'];
    $card_number     = $row['card_number'];
    $daily_limit     = $row['daily_limit'];
    $remaining_limit = $row['remaining_limit'];
} else {
    echo "Error: Unable to fetch card details.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ATM Services</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- (Optional) Font Awesome for icons -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSsRgjOMZZlC2T9bFVXudxntoHvfZqNn1aI6P48baG9aD7Tr7mv4EX+mkT2UgQ=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />

  <!-- Your JS file for ATM logic -->
  <script src="atm_services.js" defer></script>

  <style>
    /* Slight hover/focus scale + shadow on interactive elements */
    .animate-focus:focus {
      transform: scale(1.03);
      box-shadow: 0 0 12px rgba(66, 153, 225, 0.4);
    }
    .animate-hover:hover {
      transform: scale(1.03);
    }
  </style>
</head>
<body class="bg-gradient-to-r from-blue-200 to-blue-400 min-h-screen flex items-start justify-center pt-10">
  <!-- Main container / card -->
  <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold text-gray-700 mb-4">ATM Services</h2>

    <!-- Card blocked message -->
    <p
      id="block-message"
      class="text-red-600 mb-4 <?php echo ($is_blocked === 'yes') ? '' : 'hidden'; ?>"
    >
      Your card is blocked. ATM services are disabled.
    </p>

    <!-- PIN prompt section -->
    <div
      id="pin-prompt"
      class="<?php echo ($is_blocked === 'yes') ? 'hidden' : ''; ?>"
    >
      <p class="text-gray-700 mb-2 font-semibold">Please enter your PIN:</p>
      <div class="flex gap-3">
        <input
          type="password"
          id="pin-input"
          maxlength="4"
          placeholder="Enter 4-digit PIN"
          class="border rounded px-3 py-2 focus:outline-none focus:ring-2 
                 focus:ring-blue-400 animate-focus transition-all duration-300"
        />
        <button
          onclick="checkPin()"
          class="bg-blue-600 text-white font-semibold px-4 py-2 rounded 
                 transition-transform transform animate-hover hover:bg-blue-700"
        >
          Submit
        </button>
      </div>
      <p id="pin-error-message" class="text-red-600 mt-2 hidden"></p>
    </div>

    <!-- Available Balance & Limits (hidden until PIN is correct) -->
    <p
      class="mt-4 font-semibold text-gray-700 hidden"
      id="available-balance-section"
    >
      Available Balance: 
      <span id="available-balance" class="text-gray-800 font-bold ml-1">
        <?php echo $balance; ?>
      </span>
    </p>
    <p
      class="font-semibold text-gray-700 hidden"
      id="daily-limit-section"
    >
      Daily Limit: 
      <span id="daily-limit" class="text-gray-800 font-bold ml-1">
        <?php echo $daily_limit; ?>
      </span>
    </p>
    <p
      class="font-semibold text-gray-700 hidden mb-4"
      id="remaining-limit-section"
    >
      Remaining Daily Limit: 
      <span id="remaining-limit" class="text-gray-800 font-bold ml-1">
        <?php echo $remaining_limit; ?>
      </span>
    </p>

    <!-- Success message -->
    <p
      id="success-message"
      class="text-green-600 font-semibold mb-4 hidden"
    ></p>

    <!-- Deposit Option -->
    <div
      id="deposit-option"
      class="hidden mb-4"
    >
      <button
        type="button"
        class="bg-blue-600 text-white font-semibold px-4 py-2 rounded 
               transition-transform transform animate-hover hover:bg-blue-700"
        onclick="showDeposit()"
      >
        Deposit
      </button>
      <!-- Deposit Field -->
      <div
        id="deposit-field"
        class="mt-2 hidden"
      >
        <div class="flex gap-3 items-center">
          <input
            type="number"
            id="deposit-amount"
            placeholder="Enter amount to deposit"
            min="1"
            class="border rounded px-3 py-2 focus:outline-none focus:ring-2 
                   focus:ring-blue-400 animate-focus transition-all duration-300 w-full"
          />
          <button
            type="button"
            class="bg-green-600 text-white font-semibold px-4 py-2 rounded 
                   transition-transform transform animate-hover hover:bg-green-700"
            onclick="depositAmount()"
          >
            Deposit
          </button>
        </div>
      </div>
    </div>

    <!-- Withdraw Option -->
    <div
      id="withdraw-option"
      class="hidden mb-4"
    >
      <button
        type="button"
        id="withdraw-btn"
        <?php echo ($remaining_limit <= 0 ? 'disabled' : ''); ?>
        class="bg-blue-600 text-white font-semibold px-4 py-2 rounded 
               transition-transform transform animate-hover hover:bg-blue-700
               <?php echo ($remaining_limit <= 0 ? 'opacity-50 cursor-not-allowed' : ''); ?>"
        onclick="showWithdraw()"
      >
        Withdraw
      </button>
      <p
        id="withdraw-limit-message"
        class="text-red-600 mt-2 <?php echo ($remaining_limit <= 0 ? '' : 'hidden'); ?>"
      >
        Withdrawal not available. Limit exceeded. Try again after 24 hours.
      </p>
      <!-- Withdraw Field -->
      <div
        id="withdraw-field"
        class="mt-2 hidden"
      >
        <div class="flex gap-3 items-center">
          <input
            type="number"
            id="withdraw-amount"
            placeholder="Enter amount to withdraw"
            min="1"
            class="border rounded px-3 py-2 focus:outline-none focus:ring-2 
                   focus:ring-blue-400 animate-focus transition-all duration-300 w-full"
          />
          <button
            type="button"
            class="bg-green-600 text-white font-semibold px-4 py-2 rounded 
                   transition-transform transform animate-hover hover:bg-green-700"
            onclick="withdrawAmount()"
          >
            Withdraw
          </button>
        </div>
      </div>
    </div>

    <!-- Error message -->
    <p
      id="error-message"
      class="text-red-600 font-semibold hidden"
    ></p>
  </div>

  <script>
    // EXACT SAME LOGIC AS YOUR PREVIOUS CODE

    const storedPin = '<?php echo $pin; ?>';
    let attemptsLeft = <?php echo $attempts; ?>;
    const cardNumber = '<?php echo $card_number; ?>';
    let remainingLimit = <?php echo $remaining_limit; ?>;
    const dailyLimit = <?php echo $daily_limit; ?>;

    const pinPrompt = document.getElementById('pin-prompt');
    const blockMessage = document.getElementById('block-message');
    const pinErrorMessage = document.getElementById('pin-error-message');
    const availableBalanceSection = document.getElementById('available-balance-section');
    const dailyLimitSection = document.getElementById('daily-limit-section');
    const remainingLimitSection = document.getElementById('remaining-limit-section');
    const depositOption = document.getElementById('deposit-option');
    const withdrawOption = document.getElementById('withdraw-option');
    const withdrawLimitMessage = document.getElementById('withdraw-limit-message');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');

    function checkPin() {
      const enteredPin = document.getElementById('pin-input').value.trim();
      if (enteredPin === storedPin) {
        pinPrompt.classList.add('hidden');
        availableBalanceSection.classList.remove('hidden');
        dailyLimitSection.classList.remove('hidden');
        remainingLimitSection.classList.remove('hidden');
        depositOption.classList.remove('hidden');
        withdrawOption.classList.remove('hidden');
      } else {
        attemptsLeft--;
        pinErrorMessage.classList.remove('hidden');
        pinErrorMessage.textContent = `Incorrect PIN. ${attemptsLeft} attempts left.`;

        if (attemptsLeft === 0) {
          blockCard();
        } else {
          updateAttempts(attemptsLeft);
        }
      }
    }

    function showDeposit() {
      document.getElementById('deposit-field').classList.remove('hidden');
      document.getElementById('withdraw-field').classList.add('hidden');
      successMessage.classList.add('hidden');
      errorMessage.classList.add('hidden');
    }

    function depositAmount() {
      const depositVal = parseFloat(document.getElementById('deposit-amount').value);
      if (isNaN(depositVal) || depositVal <= 0) {
        showError('Please enter a valid deposit amount.');
        return;
      }
      updateBalanceAndLimitInDB(depositVal, 'Deposit');
    }

    function showWithdraw() {
      document.getElementById('withdraw-field').classList.remove('hidden');
      document.getElementById('deposit-field').classList.add('hidden');
      successMessage.classList.add('hidden');
      errorMessage.classList.add('hidden');
    }

    function withdrawAmount() {
      const withdrawVal = parseFloat(document.getElementById('withdraw-amount').value);
      if (isNaN(withdrawVal) || withdrawVal <= 0) {
        showError('Please enter a valid withdrawal amount.');
        return;
      }
      updateBalanceAndLimitInDB(withdrawVal, 'Withdraw');
    }

    function showError(msg) {
      errorMessage.textContent = msg;
      errorMessage.classList.remove('hidden');
      successMessage.classList.add('hidden');
    }

    function showSuccess(msg) {
      successMessage.textContent = msg;
      successMessage.classList.remove('hidden');
      errorMessage.classList.add('hidden');
    }

    function blockCard() {
      fetch('manage_card_backend.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=block&card_number=${cardNumber}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          blockMessage.classList.remove('hidden');
          pinPrompt.classList.add('hidden');
          availableBalanceSection.classList.add('hidden');
          dailyLimitSection.classList.add('hidden');
          remainingLimitSection.classList.add('hidden');
          depositOption.classList.add('hidden');
          withdrawOption.classList.add('hidden');
          pinErrorMessage.classList.add('hidden');
        }
      });
    }

    function updateAttempts(remaining) {
      fetch('manage_card_backend.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update_attempts&card_number=${cardNumber}&attempts=${remaining}`
      });
    }

    // Adjust daily limit or remaining limit after deposit/withdraw
    function updateBalanceAndLimitInDB(amount, actionType) {
      // Your existing fetch or AJAX logic to update on the server side
      // After success, update the UI accordingly
    }
  </script>
</body>
</html>
