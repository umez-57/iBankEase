<?php
session_start();
include 'db_connect.php'; // Include your database connection

// Ensure the user is logged in
if (!isset($_SESSION['user'])) {
    echo "Error: No user is logged in.";
    exit();
}

$username = $_SESSION['user'];

// Fetch full name from the users table
$sql_user = "SELECT fullname FROM users WHERE username = '$username'";
$result_user = mysqli_query($conn, $sql_user);

if (mysqli_num_rows($result_user) > 0) {
    $user = mysqli_fetch_assoc($result_user);
    $fullname = $user['fullname'];
} else {
    echo "Error: User not found.";
    exit();
}

// Fetch card details, including the is_blocked status
$sql_card = "SELECT * FROM cards WHERE account_number = (SELECT account_number FROM customer_accounts WHERE username = '$username')";
$result_card = mysqli_query($conn, $sql_card);

if (mysqli_num_rows($result_card) > 0) {
    $card = mysqli_fetch_assoc($result_card);
    $card_status = ($card['is_blocked'] === 'yes') ? 'Blocked' : 'Unblocked';
    $status_color = ($card['is_blocked'] === 'yes') ? 'red' : 'green';
} else {
    echo "Error: No card found for this user.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Your Card</title>

  <!-- We keep your existing styling, just below we've added a gradient background 
       and hover transitions for the buttons. -->
  <style>
    body {
      font-family: Arial, sans-serif;
      /* New gradient background */
      background: linear-gradient(to right, #ece9e6, #ffffff);
      margin: 0;
      padding: 0;
    }

    .card-container {
      width: 500px;
      margin: 50px auto;
      padding: 20px;
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .card-details {
      background-image: url('https://i.ibb.co/3zwShvP/Picsart-23-10-30-14-58-19-511.png');
      background-size: 100% 100%;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      height: 300px;
      display: flex;
      flex-direction: column;
      color: #ffffff;
      font-size: 24px;
    }

    .card-details p {
      font-size: 16px;
      color: #ffffff;
    }

    #num {
      margin-top: 70px;
      margin-left: 75px;
    }
    #exp {
      margin-left: 190px;
    }
    #cvv {
      margin-left: 190px;
    }

    .manage-options input,
    .manage-options button {
      width: 90%;
      padding: 12px;
      margin: 10px auto;
      border-radius: 5px;
      border: 1px solid #ddd;
      font-size: 14px;
      transition: all 0.3s ease; /* for hover animation */
    }

    .manage-options input {
      background-color: #f9f9f9;
    }

    /* The "Set Daily Limit" and "Change PIN" buttons */
    .manage-options button {
      background-color: #007bff;
      color: white;
      border: none;
      cursor: pointer;
    }
    /* Button hover: color + slight scale */
    .manage-options button:hover {
      background-color: #0056b3;
      transform: scale(1.03);
    }

    #success-message {
      margin-top: 20px;
      font-weight: bold;
      color: #28a745;
    }

    #block,
    #unblock {
      width: 90%;
      padding: 15px;
      font-size: 20px;
      font-weight: bold;
      color: white;
      background-color: #dc3545;
      border: none;
      border-radius: 5px;
      margin: 10px auto;
      cursor: pointer;
      width: 200px;
      transition: all 0.3s ease; /* for hover animation */
    }

    #block:hover {
      background-color: #c82333;
      transform: scale(1.03);
    }

    #unblock {
      background-color: #28a745;
    }
    #unblock:hover {
      background-color: #218838;
      transform: scale(1.03);
    }

    #new-daily-limit,
    #new-pin {
      width: 400px;
    }

    #card-status {
      margin-top: 15px;
      font-weight: bold;
      font-size: 18px;
    }
  </style>

  <script src="manage_card.js" defer></script>
</head>
<body>
  <div class="card-container">
    <h2><?php echo htmlspecialchars($fullname); ?>'s Card Details</h2>

    <div class="card-details">
      <p id="num">
        Card Number: 
        <span id="card-number"><?php echo htmlspecialchars($card['card_number']); ?></span>
      </p>
      <p id="exp">
        Expiry Date: <?php echo date("m/y", strtotime($card['expiry_date'])); ?>
      </p>
      <p id="cvv">
        CVV: 
        <span id="cvv-hidden">•••</span>
        <button id="cvv-toggle" onclick="showCVV('<?php echo $card['cvv']; ?>')">
          Show
        </button>
      </p>
    </div>

    <!-- Card Status Display -->
    <div 
      id="card-status"
      style="color: <?php echo $status_color; ?>;"
    >
      Current Status: <?php echo $card_status; ?>
    </div>

    <h3>Manage Card</h3>
    <div class="manage-options">
      <button
        id="block"
        onclick="blockUnblockCard('<?php echo $card['card_number']; ?>', 'block')"
      >
        Block Card
      </button>

      <button
        id="unblock"
        onclick="blockUnblockCard('<?php echo $card['card_number']; ?>', 'unblock')"
      >
        Unblock Card
      </button>

      <input
        type="number"
        id="new-daily-limit"
        placeholder="Enter new daily limit"
        min="1"
      >
      <button
        style="width: 400px;"
        onclick="changeDailyLimit('<?php echo $card['card_number']; ?>')"
      >
        Set Daily Limit
      </button>

      <input
        type="password"
        id="new-pin"
        placeholder="Enter new 4-digit PIN"
        minlength="4"
        maxlength="4"
      >
      <button
        style="width: 400px;"
        onclick="changePin('<?php echo $card['card_number']; ?>')"
      >
        Change PIN
      </button>
    </div>

    <!-- Success/Error message -->
    <p id="success-message" style="display:none;"></p>
  </div>
</body>
</html>
