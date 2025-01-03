<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <script>
        // Show alert and redirect after a short delay
        alert("Logout successful! Redirecting to login page.");
        setTimeout(function() {
            window.location.href = "index.html";
        }, 1000); // 1-second delay before redirect
    </script>
</head>
<body>
</body>
</html>
