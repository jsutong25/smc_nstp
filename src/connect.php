<?php

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => 'smcnstp.social',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);

// Database connection details
$servername = "localhost";
$username = "testadmin";
$password = "testadminpass"; // Use your database password
$database = "smc_nstpms"; // Use your database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
