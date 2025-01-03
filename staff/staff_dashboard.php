<?php
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
?>
