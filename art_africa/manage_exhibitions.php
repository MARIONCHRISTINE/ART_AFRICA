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

// Handle exhibition editing logic here
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["updateExhibition"])) {
        $exhibitionID = $_POST["exhibitionID"];
        $newExhibitionName = $_POST["newExhibitionName"];

        // Update the exhibition name in the database
        $updateStmt = $conn->prepare("UPDATE exhibition SET ExhibitionName = ? WHERE ExhibitionID = ?");
        $updateStmt->bind_param("si", $newExhibitionName, $exhibitionID);

        if ($updateStmt->execute()) {
            // Redirect to the same page after successful update
            header("Location: manage_exhibitions.php");
            exit;
        } else {
            echo "Error updating exhibition: " . $updateStmt->error;
        }

        $updateStmt->close();
    } elseif (isset($_POST["updateExhibitionURL"])) {
        $exhibitionID = $_POST["exhibitionID"];
        $newExhibitionURL = $_POST["newExhibitionURL"];

        // Update the exhibition URL in the database
        $updateURLStmt = $conn->prepare("UPDATE exhibition SET ExhibitionURL = ? WHERE ExhibitionID = ?");
        $updateURLStmt->bind_param("si", $newExhibitionURL, $exhibitionID);

        if ($updateURLStmt->execute()) {
            // Redirect to the same page after successful update
            header("Location: manage_exhibitions.php");
            exit;
        } else {
            echo "Error updating exhibition URL: " . $updateURLStmt->error;
        }

        $updateURLStmt->close();
    }
}

// Retrieve the artist's exhibitions from the database
$artistExhibitionsStmt = $conn->prepare("SELECT ExhibitionID, ExhibitionName, StartDate, EndDate, Description, ExhibitionURL FROM exhibition WHERE ArtistID = ?");
$artistExhibitionsStmt->bind_param("i", $_SESSION['user_id']);
$artistExhibitionsStmt->execute();
$artistExhibitionsResult = $artistExhibitionsStmt->get_result();
$artistExhibitionsStmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="manage.css">
    <title>Manage Exhibitions</title>
</head>
<body>

    <h2>Manage Exhibitions</h2>

    <?php
    // Display the artist's exhibitions
    while ($row = $artistExhibitionsResult->fetch_assoc()) {
        echo "<div>";
        echo "<h3>" . $row['ExhibitionName'] . "</h3>";
        echo "<p><strong>Start Date:</strong> " . $row['StartDate'] . "</p>";
        echo "<p><strong>End Date:</strong> " . $row['EndDate'] . "</p>";
        echo "<p><strong>Description:</strong> " . $row['Description'] . "</p>";
        echo "<p><strong>Exhibition URL:</strong> " . $row['ExhibitionURL'] . "</p>";
        echo "<form method='POST'>";
        echo "<input type='hidden' name='exhibitionID' value='" . $row['ExhibitionID'] . "'>";
        echo "<label for='newExhibitionName'>New Exhibition Name:</label>";
        echo "<input type='text' name='newExhibitionName' required>";
        echo "<input type='submit' name='updateExhibition' value='Update Name'>";
        echo "</form>";
        echo "<form method='POST'>";
        echo "<input type='hidden' name='exhibitionID' value='" . $row['ExhibitionID'] . "'>";
        echo "<label for='newExhibitionURL'>New Exhibition URL:</label>";
        echo "<input type='text' name='newExhibitionURL' required>";
        echo "<input type='submit' name='updateExhibitionURL' value='Update URL'>";
        echo "</form>";
        echo "<a href='delete_exhibition.php?exhibitionID=" . $row['ExhibitionID'] . "'>Delete</a>";
        echo "</div>";
    }
    ?>

    <a href="artist_dashboard.php">Back to Dashboard</a>

</body>
</html>
