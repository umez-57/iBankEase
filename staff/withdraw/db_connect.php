<?php
<<<<<<< HEAD
$servername = "localhost";
$username = "root"; // default username for XAMPP/WAMP is usually 'root'
$password = ""; // default password for XAMPP/WAMP is usually an empty string
$dbname = "banking_system"; // ensure this matches your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
=======
// Aiven MySQL details from your console:
$host       = "mysql-85726d9-umesh57-9f48.b.aivencloud.com";
$port       = 20298;  // Aiven port
$username   = "avnadmin";
$password   = "AVNS_LhbPYjN2KlyqjCMoeOB";
$dbname     = "defaultdb";

// Path to your Aiven CA certificate on your local/hosted server:
// If the file is named exactly "ca" (no extension), or "ca.pem", adjust accordingly.
$sslCaPath  = "C:\\Users\\umesh\\Downloads\\sql\\ca.pem"; 
// or "C:\\Users\\umesh\\Downloads\\sql\\ca" if the file truly has no extension

// 1. Initialize mysqli
$conn = mysqli_init();
if (!$conn) {
    die("mysqli_init() failed");
}

// 2. Configure SSL for the connection
mysqli_ssl_set($conn, NULL, NULL, $sslCaPath, NULL, NULL);

// 3. Real connect with SSL
if (!mysqli_real_connect(
    $conn,
    $host,
    $username,
    $password,
    $dbname,
    $port,
    NULL,
    MYSQLI_CLIENT_SSL
)) {
    die("Connection error: " . mysqli_connect_error());
}

// (Optional) Set the character set to utf8mb4
if (!mysqli_set_charset($conn, "utf8mb4")) {
    die("Error loading character set utf8mb4: " . mysqli_error($conn));
}

// If we reach here, $conn is a valid SSL-encrypted connection to Aiven MySQL!
>>>>>>> 46e86b5 (Initial commit of my banking system project)
?>
