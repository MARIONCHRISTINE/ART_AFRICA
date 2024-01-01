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

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email']) || !isset($_SESSION['user_type'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $receiverID = $_POST["receiver"];
    $messageText = $_POST["message"];

    // Insert message into the database
    $insertMessageStmt = $conn->prepare("INSERT INTO messages (SenderID, ReceiverID, MessageText) VALUES (?, ?, ?)");
    $insertMessageStmt->bind_param("iis", $_SESSION['user_id'], $receiverID, $messageText);
    $insertMessageStmt->execute();

    $insertMessageStmt->close();
}

$conn->close();

// Redirect back to the direct messages page
header("Location: direct_messages.php");
exit;
?>
