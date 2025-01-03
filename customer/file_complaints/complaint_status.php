<?php
session_start();
include 'db_connect.php'; // Database connection

// Check if the user is logged in and is a customer
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'customer') {
    echo "Unauthorized access!";
    exit();
}

$username = $_SESSION['user']; // Logged-in customer's username

// Fetch all complaints filed by the logged-in customer
$sql = "
    SELECT complaint_id, title, category, status, created_at
    FROM complaints
    WHERE username = '$username'
    ORDER BY complaint_id DESC
";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Complaints</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-100 to-blue-200 min-h-screen py-10 px-4">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-700">Your Complaints</h1>

    <div class="overflow-x-auto">
      <table class="w-full table-auto border-collapse">
        <!-- Table Head -->
        <thead>
          <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal font-bold">
            <th class="py-3 px-4 text-left border-b">Complaint ID</th>
            <th class="py-3 px-4 text-left border-b">Title</th>
            <th class="py-3 px-4 text-left border-b">Category</th>
            <th class="py-3 px-4 text-left border-b">Status</th>
            <th class="py-3 px-4 text-left border-b">Date & Time</th>
          </tr>
        </thead>

        <!-- Table Body -->
        <tbody class="text-gray-600 text-sm font-semibold">
          <?php if (mysqli_num_rows($result) > 0) : ?>
            <?php while ($complaint = mysqli_fetch_assoc($result)) : ?>
              <?php
                // Color-coding rows based on complaint status
                // 'Pending' => Yellow background, 'Resolved' => Green background (for example)
                $rowClass = ($complaint['status'] === 'Pending')
                  ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-800'
                  : 'bg-green-50 hover:bg-green-100 text-green-800';
              ?>
              <tr class="border-b border-gray-200 <?php echo $rowClass; ?>">
                <td class="py-3 px-4 whitespace-nowrap">
                  <?php echo str_pad($complaint['complaint_id'], 5, '0', STR_PAD_LEFT); ?>
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                  <?php echo htmlspecialchars($complaint['title']); ?>
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                  <?php echo htmlspecialchars($complaint['category']); ?>
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                  <?php echo htmlspecialchars($complaint['status']); ?>
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                  <?php echo $complaint['created_at']; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else : ?>
            <tr>
              <td colspan="5" class="py-3 px-4 text-center text-gray-500">
                No complaints found.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
