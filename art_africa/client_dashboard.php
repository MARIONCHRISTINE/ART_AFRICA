<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "art_africa";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in and has the client user type
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Client') {
    // Redirect to the login page if not logged in or not a client
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="client.css">
    <title>Client Dashboard</title>
</head>
<body>

    <h2>Welcome, <?php echo $_SESSION['email']; ?> (Client)</h2>

    <!-- Client-specific links -->
    <ul>
        <li><a href="update_profile.php">Update Profile</a></li>
        <li><a href="check_exhibitions.php">Exhibitions</a></li>
        <li><a href="quick_search.php">Quick Search</a></li>
        <li><a href="direct_messages.php">Inbox</a></li>
        <li><a href="transactions.php">Transactions</a></li>
        <li><a href="allart.php">Artworks</a></li>
    </ul>

    <a href="logout.php">Logout</a>

</body>
</html>
