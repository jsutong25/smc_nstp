<?php

$servername = "localhost";
$username = "root";
$password = ""; // Use your database password
$database = "smc_nstpms"; // Use your database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
