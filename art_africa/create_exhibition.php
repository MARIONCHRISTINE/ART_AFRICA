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

// Check if the user is logged in and has the artist user type
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Artist') {
    // Redirect to the login page if not logged in or not an artist
    header("Location: login.php");
    exit;
}

// Handle exhibition creation logic here

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve exhibition information
    $exhibitionName = $_POST["exhibitionName"];
    $startDate = $_POST["startDate"];
    $endDate = $_POST["endDate"];
    $description = $_POST["description"];
    $artistID = $_SESSION['user_id'];

    // Insert the new exhibition into the database
    $insertStmt = $conn->prepare("INSERT INTO exhibition (ArtistID, ExhibitionName, StartDate, EndDate, Description) VALUES (?, ?, ?, ?, ?)");

    if (!$insertStmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $insertStmt->bind_param("issss", $artistID, $exhibitionName, $startDate, $endDate, $description);

    if ($insertStmt->execute()) {
        // Redirect to the artist dashboard after successful exhibition creation
        header("Location: artist_dashboard.php");
        exit;
    } else {
        echo "Error creating exhibition: " . $insertStmt->error;
    }

    $insertStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="exhibition.css">
    <title>Create Exhibition</title>
</head>
<body>

    <h2>Create Exhibition</h2>

    <form action="" method="POST">
        <label for="exhibitionName">Exhibition Name:</label>
        <input type="text" name="exhibitionName" required><br>

        <label for="startDate">Start Date:</label>
        <input type="date" name="startDate" required><br>

        <label for="endDate">End Date:</label>
        <input type="date" name="endDate" required><br>

        <label for="description">Description:</label>
        <textarea name="description" rows="4" required></textarea><br>

        <input type="submit" value="Create Exhibition">
    </form>

    <a href="artist_dashboard.php">Back to Dashboard</a>

</body>
</html>
