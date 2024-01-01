<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the landing page (adjust the URL accordingly)
header("Location: landing_page.php");
exit;
?>
