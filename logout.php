<?php
session_start();

// Destroy session only if it exists
if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset(); // Unset session variables
    session_destroy(); // Destroy session
}

header("Location: index.php"); // Redirect to homepage after logout
exit();
?>
