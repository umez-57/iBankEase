<?php
session_start();
include 'db_connect.php'; // Database connection

// Check if the user is logged in and is a customer
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'customer') {
    echo "Unauthorized access!";
    exit();
}

$username = $_SESSION['user']; // Logged-in customer's username
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>File a New Complaint</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- External JS for form handling (unchanged) -->
  <script src="complaint.js" defer></script>

  <style>
    /* Optional custom focus or hover animations */
    .animate-focus:focus {
      transform: scale(1.02);
      box-shadow: 0 0 12px rgba(66, 153, 225, 0.4);
    }
    .animate-hover:hover {
      transform: scale(1.02);
    }
  </style>
</head>

<body class="bg-gradient-to-r from-blue-100 to-blue-200 min-h-screen flex items-start justify-center pt-10">
  <!-- Container -->
  <div class="max-w-md w-full bg-white shadow-lg rounded p-6 mx-4">
    <h1 class="text-2xl font-bold mb-6 text-gray-700">File a New Complaint</h1>

    <!-- Message area for success or error messages -->
    <div id="message" class="text-center text-sm mb-4 hidden"></div>

    <!-- Complaint Form -->
    <form id="complaintForm" class="space-y-4">
      <!-- Complaint Title -->
      <div>
        <label for="title" class="block text-gray-700 font-semibold mb-1">
          Complaint Title:
        </label>
        <input
          type="text"
          id="title"
          name="title"
          required
          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 
                 focus:ring-blue-400 transition-all duration-300 animate-focus"
        />
      </div>

      <!-- Category -->
      <div>
        <label for="category" class="block text-gray-700 font-semibold mb-1">
          Category:
        </label>
        <select
          id="category"
          name="category"
          required
          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 
                 focus:ring-blue-400 transition-all duration-300"
        >
          <option value="Card Issue">Card Issue</option>
          <option value="Login Issue">Login Issue</option>
          <option value="Transaction Issue">Transaction Issue</option>
          <option value="Account Information">Account Information</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <!-- Description -->
      <div>
        <label for="description" class="block text-gray-700 font-semibold mb-1">
          Description:
        </label>
        <textarea
          id="description"
          name="description"
          rows="5"
          required
          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 
                 focus:ring-blue-400 transition-all duration-300 animate-focus"
        ></textarea>
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        class="w-full bg-blue-600 text-white font-semibold py-2 rounded 
               transition-transform transform animate-hover hover:bg-blue-700"
      >
        Submit Complaint
      </button>
    </form>
  </div>
</body>
</html>
